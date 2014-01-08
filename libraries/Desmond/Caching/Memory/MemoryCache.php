<?php

Application::Import('Desmond::Caching::Memory::IMemoryCache.php');


class MemoryCache implements IMemoryCache {
	private $size = 10000;
	private $handlers = array();
	
	function __construct() {
		/* read in handlers */
		$this->handlers = json_decode(file_get_contents(Application::path('temp') . 'cache/memory/handlers'), true);
	}
	
	function CreateMemoryHandler($name) {

		/* create new handler in memory */
		$id = microtime(false) + rand(0,100000);

		/* update by assigning memory block to new id */

		 $shm = shm_attach($id, $this->size);
         $mutex = sem_get($id, 1);

         $this->handlers[$name] = $id;

         /* write out handlers */
         file_put_contents(Application::path('temp') . 'cache/memory/handlers', json_encode($this->handlers));

	}
	
	function WriteCacheData($handler, $data, $time) {

		$id = $this->handlers[$handler];

		$shm = shm_attach($id, $this->size);
        $mutex = sem_get($id, 1);

        $data['expired_time'] = $time;

        sem_acquire($mutex);    //block until released
        shm_put_var($shm, $id, json_encode($data, true));    //store var 
        sem_release($mutex);    //release mutex  
	}
	
	function ReadCacheData($handler) {

		$id = $this->handlers[$handler];

		$shm = shm_attach($id, $this->size);
        $mutex = sem_get($id, 1);

        sem_acquire($mutex);    //block until released
        $var_array = @json_decode(shm_get_var($shm, $id), true);    //read var        
        sem_release($mutex);    //release mutex 
        unset($var_array['expired_time']);

		return $var_array;
	}
	
	function IsExpired($handler) {

		if(!isset($this->handlers[$handler])) {
			return false;
		}

		$id = $this->handlers[$handler];

		$shm = shm_attach($id, $this->size);
        $mutex = sem_get($id, 1);

        sem_acquire($mutex);    //block until released
        $var_array = @json_decode(shm_get_var($shm, $id), true);    //read var        
        sem_release($mutex);    //release mutex 

		if(time() > $var_array['expired_time']) 
		{
		
			return true;
		}
		
		else {
			return false;
		}
	}
	
	function IsCreated($handler) {
		if(isset($this->handlers[$handler]))
		{
			return true;
		}
		
		else {
			return false;
		}
	}
}
?>

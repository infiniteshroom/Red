<?php
date_default_timezone_set('UTC');
class DesmondLogger {
	
    public function Write($data, $type='errors') {
		if(Application::Setting('logs::enable') != true) {
			/* if logs are turned off halt the logger here */
			return false;		
		}

		 //let's first wirte our log message to it's type based file
		$today = date("m-d-y");
		$time = date("H:i:s"); 
		
		$typepath = Application::Setting('logs::path') . "$type/$today.log";
		 
	    $filetypehandler = (file_exists($typepath))? fopen($typepath, "a+") : fopen($typepath, "w+");
	    fwrite($filetypehandler, "[$today $time][$type] $data \r\n");
	    fclose($filetypehandler);
	    
	    //let's lastly add the log to the complete log file
	    $completepath = Application::Setting('logs::path') . "complete/$today.log";
	    $filecompletehandler = (file_exists($completepath))? fopen($completepath, "a+") : fopen($completepath, "w+");
	    fwrite($filecompletehandler, "[$today $time][$type] $data \r\n");
	    fclose($filecompletehandler);

	}
	
	public function Read() {
		$today = date("m-d-y");
		
		$filepath = Application::Setting('logs::path') . 'complete/' . $today . '.log';
		
		if(file_exists($filepath)) {
			return file_get_contents($filepath); 
		} 
		
		else {
			return "";
		}
	}
}
?>

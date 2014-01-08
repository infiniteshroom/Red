<?php
Application::Import('Desmond::Session::ISession.php');
Application::Import('Desmond::Caching::Memory::*');
ob_start();

date_default_timezone_set('UTC');
class MemorySession implements ISession {
	public $session_id = null;
	private $vars = array();
	

	public function __construct() {
		return $this->Create();
	}
	public function Get($name) {

		if(isset($this->vars[$name])) {
			return $this->vars[$name];
		}

		else {
			return "";
		}
	}
	
	public function Set($name, $value) {

		$this->vars[$name] = $value;
		$this->Save();

		return $this->vars[$name] = $value;
	}
	
	public function RememberMe($value) {
		
		if($value) {
			setcookie("RED_SID", $this->session_id, time()+86400*365, "/");
		}
		
		else {
			setcookie("RED_SID", $this->session_id, time()+3600, "/");
		}
	}
	
	public function Create($args=null) {
		//let's save our dbclass instance for querying the database
            
		//Hello Cookie are you there
        
		if (!isset($_COOKIE["RED_SID"])) 
		{
			
		//let's setup a new memory instance and return all sessions	
		$memory = new MemoryCache();
		$sessions = array();
		if($memory->IsExpired('RED_SID') || $memory->IsCreated('RED_SID') == false) 
		{
			//so there's no sessions, yet someone has a cookie. There defo baked =p destory "session" and return from script
			$memory->CreateMemoryHandler('RED_SID');
			$memory->WriteCacheData('RED_SID', array(), strtotime("+1 year"));	 
		}

		else {
			$sessions = $memory->ReadCacheData('RED_SID');	
		}
		
	
		
			//Gen UUID. TODO: Add blowfish crypt of sessionID to store in cookie.
			$this->session_id = md5(uniqid());
			$sessions[$this->session_id] = array(
				'user_id' => -1,
				'user_group_id' => 3,
				'session_start' => date('Ymd'),
				'session_end' => date("Ymd"),
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'user_ip' => $_SERVER['REMOTE_ADDR']
			);
			
			$memory->WriteCacheData('RED_SID', $sessions, strtotime("+1 year"));
			
			$sessions = $memory->ReadCacheData('RED_SID');
			$session = $sessions[$this->session_id];
			
			$this->vars = $session;
			setcookie("RED_SID", $this->session_id, time()+3600, "/");
			header("location: ./");
		}
		else 
		{
				$memory = new MemoryCache();
				$sessions = array();
				if($memory->IsExpired('RED_SID') || $memory->IsCreated('RED_SID') == false) 
				{
					//so there's no sessions, yet someone has a cookie. There defo baked =p destory "session" and return from script
					$memory->CreateMemoryHandler('RED_SID');
					$memory->WriteCacheData('RED_SID', array(), strtotime("+1 year"));
					$this->Destroy();
				    header("location: ./");
					 
				}

				else {
					$sessions = $memory->ReadCacheData('RED_SID');	
				}
				
				$session = $sessions[$_COOKIE['RED_SID']];
				//Hey don't spoof our session, we don't like that.
				if($session['user_agent'] == $_SERVER['HTTP_USER_AGENT'] && $session['user_ip'] == $_SERVER['REMOTE_ADDR'] ) 
				{
				   $this->session_id = $_COOKIE["RED_SID"];
				   $this->vars = $session;
				}
				else 
				{
					//Well you had to didn't you. You attempted to spoof, we destory. Though we should regen ID, but this will do for now.
					$this->session_id = $_COOKIE["RED_SID"];
					$this->Destroy();
				    header("location: ./");
				}
		}
	}

	public function Destroy() {
		if (isset($_COOKIE["RED_SID"])) 
		{
			
			
			$memory = new MemoryCache();
			$sessions = array();
			if($memory->IsExpired('RED_SID') || $memory->IsCreated('RED_SID') == false) 
			{
				//so there's no sessions, yet someone has a cookie. There defo baked =p destory "session" and return from script
				$memory->CreateMemoryHandler('RED_SID');
				$memory->WriteCacheData('RED_SID', array(), strtotime("+1 year"));	 
			}

			else {
				$sessions = $memory->ReadCacheData('RED_SID');	
			}
			
			if(array_key_exists($this->session_id, $sessions)) {

				
				unset($sessions[$this->session_id]);
				
				
				$memory->WriteCacheData('RED_SID', $sessions, strtotime("+1 year"));
				
				if($sessions != $memory->ReadCacheData('RED_SID')) {
					throw new SessionCannotDestorySession($this->session_id);
				}
			}
			
			setcookie("RED_SID", "", time()-3600, "/");
		}
	}

	public function Save() {
		
		$memory = new MemoryCache();
		$sessions = array();
		if($memory->IsExpired('RED_SID') || $memory->IsCreated('RED_SID') == false) 
			{
					
				$memory->CreateMemoryHandler('RED_SID');
				$memory->WriteCacheData('RED_SID', array(), strtotime("+1 year"));	 
			}

			else {
				
				$sessions = $memory->ReadCacheData('RED_SID');
				$sessions[$this->session_id] = $this->vars;
				$memory->WriteCacheData('RED_SID', $sessions, strtotime("+1 year"));
				
				if($sessions != $memory->ReadCacheData('RED_SID')) {
					throw new SessionCannotSaveSession($this->session_id);
				}
			}
	} 
}
ob_end_clean();

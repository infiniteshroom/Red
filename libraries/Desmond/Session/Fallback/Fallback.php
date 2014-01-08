<?php
Application::Import('Desmond::Session::ISession.php');
class SessionFallback implements ISession{
	function __construct() {
		$this->Create();
	}
	
	public function Create($args=null) {
		
		session_start();
		
		//did the user trying to access the session initated it?
		if (!isset($_SESSION['initiated']))
		{
			//no? then regenerate the session id
			session_regenerate_id();
			$_SESSION['initiated'] = true;
		}

		else {
		  //valid session store the default session vars
		  $_SESSION['user_id'] = -1;
		  $_SESSION['user_group_id'] = 3;
		}
		
		//is the user who initated the session using the same user agent?
		if (isset($_SESSION['HTTP_USER_AGENT']))
		{
			if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
			{
				//no? then exit the script for security
				exit;
			}
		}
	else
	{
		//store MD5 hash of useragent to avoid it being tempared with
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
}
	
	public function RememberMe($value) {
		//stub	
	}
	public function Set($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	public function Get($name) {

		if(isset($_SESSION[$name])) {
			return $_SESSION[$name];		
		}

		else {
			return "";
		}

	}
	
	public function Destroy() {
		session_destroy();
	}

	public function Save() {
	//session fallback doesn't need to save so stub this method
	}
	 
}
?>

<?php
Application::Import('Desmond::Session::ISession.php');
Application::Import('Desmond::Caching::Memory::*');
ob_start();

date_default_timezone_set('UTC');
class DatabaseSession implements ISession {
	public $session_id = null;
	private $vars = array();
	private $db = null;
	

	private function CreateSchema() {

		$connection = Database::GetActiveConnection();
		$query = new DesmondDatabaseQuery($connection);

		$query->Execute(
				"CREATE TABLE IF NOT EXISTS `sessions` (
				  `id` varchar(255) NOT NULL,
				  `user_id` int(11) NOT NULL,
				  `session_start` date NOT NULL,
				  `session_end` date NOT NULL,
				  `user_agent` varchar(255) NOT NULL,
				  `user_ip` varchar(255) NOT NULL,
				  `vars` text NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
			);	 
	}

	public function __construct() {

		/* easiest fix for now */
		$connection = Database::GetActiveConnection();
		$this->db = Database::table('sessions');

		try {
			$this->db->results('one');
		}

		catch(DatabaseQueryException $error) {
			$this->CreateSchema();
		}

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
		$this->db = Database::table('sessions');

            
		//Hello Cookie are you there
        
		if (!isset($_COOKIE["RED_SID"])) 
		{
			//Gen UUID. TODO: Add blowfish crypt of sessionID to store in cookie.
			$this->session_id = md5(uniqid());

			$session_vars = json_encode(array());

			$sessions_data = array(
				'id' => $this->session_id,
				'user_id' => -1,
				'session_start' => date('Ymd'),
				'session_end' => date("Ymd"),
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'user_ip' => $_SERVER['REMOTE_ADDR'],
				'vars' => $session_vars,
			);

			$this->db->insert($sessions_data);

			/* get the information back to check our session was correctly made */
			$sessions_data = $this->db->where(array('id', '=', $this->session_id))->results('array');

			$sessions_data = $sessions_data[0];
			$this->vars = $sessions_data;

			unset($this->vars['vars']);
			setcookie("RED_SID", $this->session_id, time()+3600, "/");

			HTTPResponse::Redirect('./');
		}
		else 
		{
				$session_id = $_COOKIE['RED_SID'];

				$session = $this->db->where(array('id', '=', $session_id))->results('array');

				$session = $session[0];

				//Hey don't spoof our session, we don't like that.
				if($session['user_agent'] == $_SERVER['HTTP_USER_AGENT'] && $session['user_ip'] == $_SERVER['REMOTE_ADDR'] ) 
				{
				   $this->session_id = $_COOKIE["RED_SID"];
				   $this->vars = json_decode($session['vars'], true);

				   $this->vars['user_id'] = $session['user_id'];
				   $this->vars['session_start'] = $session['session_start'];
				   $this->vars['session_end'] = $session['session_end'];
				   $this->vars['user_agent'] = $session['user_agent'];
				   $this->vars['user_ip'] = $session['user_ip']; 
				}
				else 
				{
					//Well you had to didn't you. You attempted to spoof, we destory. Though we should regen ID, but this will do for now.
					$this->session_id = $_COOKIE["RED_SID"];
					$this->Destroy();

					HTTPResponse::Redirect('./');
				}
		}
	}

	public function Destroy() {
		$this->db = Database::table('sessions');
		if (isset($_COOKIE["RED_SID"])) 
		{
			/* delete session row and unset cookie */
			$this->db->where(array('id', '=', $this->session_id))->delete();
			$this->session_id = "";
			setcookie("RED_SID", "", time()-3600, "/");
		}		
	}

	public function Save() {
		$this->db = Database::table('sessions');
		
		if (isset($_COOKIE["RED_SID"])) {
			/* save back to db */

			$session_vars = $this->vars;

			$session_vars_all = $this->vars;


			/* remove common session vars, as these have there own cols in the db */
			unset($session_vars['id']);
			unset($session_vars['user_id']);
			unset($session_vars['session_start']);
			unset($session_vars['session_end']);
			unset($session_vars['user_agent']);
			unset($session_vars['user_ip']);

			$update_data = array(
				'vars' => json_encode($session_vars),
				'user_id' => $session_vars_all['user_id'],
				'session_start' => $session_vars_all['session_start'],
				'session_end' => $session_vars_all['session_end'], 
				'user_agent' => $session_vars_all['user_agent'],
				'user_ip' => $session_vars_all['user_ip'],
			);

			$this->db->where(array('id', '=', $this->session_id))->update($update_data);
		}	
	} 
}
ob_end_clean();

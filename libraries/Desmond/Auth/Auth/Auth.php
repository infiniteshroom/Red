<?php
	Application::Import('Desmond::Auth::Auth::IAuth.php');
	Class DesmondAuth implements IAuth {

		private $driver_obj = null;

		function __construct() {
			/* get auth type */

			$type = Application::Setting('auth::type');

			Application::Import("Desmond::Auth::Auth::Drivers::{$type}.php");

			$driver_name = $type . 'Auth';
			$this->driver_obj = new $driver_name();
		}

		public function Login($username, $password) {
			return $this->driver_obj->Login($username, $password);
		} 

		public function isGuest() {
			return $this->driver_obj->isGuest();
		}


		public function Check($username, $password) {
			return $this->driver_obj->Check($username, $password);
		}	


		public function Logout() {
			return $this->driver_obj->Logout();

		}


		public function User() {
			return $this->driver_obj->User();

		}


		public function Spoof($id) {
			$this->driver_obj->Spoof($id);

		}
	}
?>
<?php
Application::import('Desmond::Auth::Password::IPassword.php');

	class DesmondPassword implements IPassword {
		protected $hash_object = null;

		function __construct() {
			/* get password type */
			$type = Application::Setting('auth::hash');


			Application::Import("Desmond::Auth::Password::Drivers::{$type}.php");

			$object_name = $type . 'Password';
			$this->hash_object = new $object_name();

		}

		public function Create($password) {
			return $this->hash_object->Create($password);
		}

		public function Check($hash, $plain) {
			return $this->hash_object->Check($hash, $plain);
		}
	}

	
?>
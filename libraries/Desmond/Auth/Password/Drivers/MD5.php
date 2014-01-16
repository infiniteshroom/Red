<?php
Application::import('Desmond::Auth::Password::IPassword.php');

	class MD5Password implements IPassword {

		public function Create($password) {
			return MD5($password);
		}

		public function Check($hash, $plain) {

	  		if(md5($plain) == $hash) {
	  			return true;
	  		}

	  		else {
	  			return false;
	  		}
		}
	}
?>
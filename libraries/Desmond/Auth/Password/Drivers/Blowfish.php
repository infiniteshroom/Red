<?php
Application::import('Desmond::Auth::Password::IPassword.php');
Application::Import('Phpass::Hasher::hasher.php');

	class BlowfishPassword implements IPassword {

		public function Create($password) {
			$Hasher = new PasswordHash(8, FALSE);
	  		return $Hasher->HashPassword($password);
		}

		public function Check($hash, $plain) {
	  		$Hasher = new PasswordHash(8, FALSE);
	  		return $Hasher->CheckPassword($plain, $hash);
		}
	}
?>
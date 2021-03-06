<?php
Application::import('Desmond::Auth::Password::IPassword.php');
Application::Import('PBKD2::Hasher::hasher.php');

	class PBKD2Password implements IPassword {

		public function Create($password) {
			$hasher = new PBKDF2();
			$hash = $hasher->create_hash($password);
			return $hash;
		}

		public function Check($hash, $plain) {

			$hasher = new PBKDF2();
			return $hasher->validate_password($plain, $hash);
		}
	}
?>
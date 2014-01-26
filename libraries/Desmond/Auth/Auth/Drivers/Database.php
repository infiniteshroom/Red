<?php
Application::Import('Desmond::Auth::Auth::IAuth.php');
	class DatabaseAuth Implements IAuth{


		/* get details */
		public function User() {
			$datasource = Application::Setting('auth::datasource');
			$table = Application::Setting('auth::table');

			$attributes = Application::Setting('auth::attributes');

			$result = Database::SetActive($datasource)->table($table)->where(array('id', '=', Session::Get('user_id')))->results('one');

			return $result;	
		}

		/* spoof user */
		public function Spoof($id) {
			Session::Set('user_id',$id);
		}


		public function Login($username, $password) {
			if($this->Check($username, $password)) {

				/* get record */

				$datasource = Application::Setting('auth::datasource');
				$table = Application::Setting('auth::table');

				$attributes = Application::Setting('auth::attributes');

				$result = Database::SetActive($datasource)->table($table)->where(array($attributes['username'], '=', $username))->results('one');

				Session::Set('user_id',$result['id']);

				return true;
			}		

			else {
				return false;
			}
		}

		public function isGuest() {
			if(Session::Get('user_id') == -1) {
				return true;
			}

			else {
				return false;
			}
		}

		public function Check($username, $password) {
			/* get settings */

			$datasource = Application::Setting('auth::datasource');
			$table = Application::Setting('auth::table');

			$attributes = Application::Setting('auth::attributes');
				

			$result_count = Database::SetActive($datasource)->table($table)->where(array($attributes['username'], '=', $username))->count();

			if($result_count > 0) {
				$result = Database::SetActive($datasource)->table($table)->where(array($attributes['username'], '=', $username))->results('one');
			}

			else {
				return false;
			}

			if(Password::Check($result[$attributes['password']], $password)) {
				return true;
			}

			else {
				return false;
			}
		}

		public function Logout() {
			Session::Set('user_id',-1);
		}
	}
?>
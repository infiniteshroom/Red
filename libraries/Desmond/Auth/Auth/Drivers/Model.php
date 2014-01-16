<?php
Application::Import('Desmond::Auth::Auth::IAuth.php');
	class ModelAuth Implements IAuth{


		/* get details */
		public function User() {
			$model = Application::Setting('auth::model');

			return $model::Find(Session::Get('user_id'));	
		}

		/* spoof user */
		public function Spoof($id) {
			Session::Set('user_id',$id);
		}


		public function Login($username, $password) {
			if($this->Check($username, $password)) {

				/* get record */

				$datasource = Application::Setting('auth::datasource');
				$model = Application::Setting('auth::model');

				$attributes = Application::Setting('auth::attributes');

				$model_obj = $model::where(array($attributes['username'], '=', $username))->results('one');

				Session::Set('user_id',$model_obj->id);
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

			$model = Application::Setting('auth::model');
			$attributes = Application::Setting('auth::attributes');

			$password_attribute = $attributes['password'];

			$model_obj = $model::where(array($attributes['username'], '=', $username))->results('one');

			if(Password::Check($model_obj->$password_attribute, $password)) {
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
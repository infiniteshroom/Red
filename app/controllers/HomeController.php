<?php
	class HomeController extends Controller {

		protected $permissions = array(
			'any_test' => 'admin',
		);

		public function any_index() {
			//return $this->response->Redirect('/home/test/');
		}

		public function any_test($id) {
			return "Hello Worlddd";
		}

		public function permission_error($method, $user) {
			return 'sorry you must be a ' . $user;
		}
	}
?>
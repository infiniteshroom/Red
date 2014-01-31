<?php
	class HomeController extends Controller {
		public function any_index() {
			//return $this->response->Redirect('/home/test/');
		}

		public function any_test($id) {
			return "Hello Worlddd";
		}
	}
?>
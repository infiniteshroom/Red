<?php
	class HomeController extends Controller {
		public function any_index() {
			$this->Set(compact('hello', 'hello world'));
		}
	}
?>
<?php
	class HomeController extends Controller {
		public function any_index() {
			$this->Set(compact('hello', 'hello world'));

			$image = new Images();
			$image->title = 'new image';

			//return $image;
			//Session::Set('test', 'hello world2');


		}

		public function post_test() {
		
		}
	}
?>
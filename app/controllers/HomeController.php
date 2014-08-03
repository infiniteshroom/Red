<?php
	class HomeController extends Controller {

		protected $permissions = array(
			//'any_test' => 'admin',
		);

		protected $events = array(
			'.test:click' => array(
			    'method' => 'test',
			    'bind' => 'p',

			    'parameters' => array(
				'name' => array(
				   '#test_name' => 'value',
				 ),
			    ),
			),
		);

		public function any_index() {
			//var_dump($test->GetErrors('inputs'));
		}

		public function any_test() {

			//Desmonds::SetFunctionProxy('HTTPRequest', 'Form', 'Post');
			//$name = HTTPRequest::Post('test');
			$name = HTTPRequest::Form('test');
			return "<p style='margin: 0 auto;color:white;display:block;'>you clicked the logo =p, and sent value $name</p>";
		}

		public function permission_error($method, $user) {
			return 'sorry you must be a ' . $user;
		}
	}
?>

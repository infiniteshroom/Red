<?php

	class DesmondCommandTests {
		private $args = array();

		function __construct($args) {
			$this->args = $args;
		}

		public function Process() {
			/* include all tests from the test folder */
			    $_SERVER['argv'][1] = Application::Path('tests') . "units/.";
			   include Application::Path('tests') . 'phpunit.phar';
		} 
	}
?>

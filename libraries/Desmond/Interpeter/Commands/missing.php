<?php
	class DesmondCommandMissing {
		private $args = array();

		function __construct($args) {
			$this->args = $args;
		}

		public function Process() {
			return "Sorry that wasn't a valid command \n";
		} 
	}
?>
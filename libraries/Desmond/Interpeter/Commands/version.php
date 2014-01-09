<?php
	class DesmondCommandVersion {
		private $args = array();

		function __construct($args) {
			$this->args = $args;
		}

		public function Process() {
			return "Red Framework V0.1 \n";
		} 
	}
?>
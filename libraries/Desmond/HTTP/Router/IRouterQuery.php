<?php
	interface IRouterQuery {
		
		public function host($host);
		public function parameter($name, $type);
		public function controller($name);
		public function action($name);
		public function method($verb);
		public function permission($group, $mode='allow');
		public function add();

		/* function must return bool */
		public function where($function);
	}
?>
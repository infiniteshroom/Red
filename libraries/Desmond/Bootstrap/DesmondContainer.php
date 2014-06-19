<?php
	class DesmondContainer {
		public $objects = array();

		public function AddDesmond($name, $object) {
			$this->objects[$name] = $object;
		}

		public function Debug() {
			var_dump($this->objects);
		}

		public function GetInstance($name) {
			return $this->objects[$name];
		}

		public function OverrideObject($name, $object) {
			$name::override($object);
		}

		public function Who($name) {
			return $name::whoami();
		}

		public function Define($name, $instance) {
			/* not always safe, the best idea is always to define your own desmond as shown in Desmonds.php */
			eval('class ' . $name.' extends DesmondObject { protected static $instance;}');

			$name::override($instance);
		}

		public function SetFunctionProxy($desmond, $function, $proxy) {

			$desmond::setProxy($function, $proxy);
		}

		public function GetFunctionProxy($desmond, $name) {
			return $desmond::getProxy($name);
		}

	}
?>
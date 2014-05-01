<?php

	class DesmondObject implements IDesmondObject {
		/* no contructon here */
		private function __construct() {
		}

		public static function instance() {
	        if (!isset(static::$instance)) {
	            static::$instance = new static();
	        }

	        return static::$instance;
	    }


		public static function override($object) {
			static::$instance = $object;

			/* setup object instance in desmond container */
			Desmonds::AddDesmond(get_called_class(), $object);

		}

		public static function whoami() {
			return get_class(static::$instance);
		}

		public static function __callStatic($name, $arguments)
	    {
	    	  if (preg_match('/^([gs]et)([A-Z])(.*)$/', $name, $match)) {
			    $reflector = new \ReflectionClass(static::$instance);
			    $property = strtolower($match[2]). $match[3];
			    if ($reflector->hasProperty($property)) {
			      $property = $reflector->getProperty($property);
			      switch($match[1]) {
			        case 'get': return $property->getValue();
			        case 'set': return $property->setValue($args[0]);
			      }     
			    } else throw new InvalidArgumentException("Property {$property} doesn't exist");
			  }

	        return call_user_func_array(array(static::$instance,$name), array_values($arguments));


	    }

	}

?>
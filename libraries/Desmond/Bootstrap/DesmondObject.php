<?php

	class DesmondObject implements IDesmondObject {


		/* proxies allow you to define function aliases for an object */
		protected static $proxies = array();

		/* no contructon here */
		private function __construct() {
		}

		public static function instance() {
	        if (!isset(static::$instance)) {
	            static::$instance = new static();
	        }

	        return static::$instance;
	    }

	    public static function getProxy($name) {
	    	$proxies = array_keys(static::$proxies);

	    	if(in_array($name, $proxies)) {
	    	  	$name = static::$proxies[$name];
	    	  	return $name;
	    	 }

	    	 else {
	    	 	return $name;
	    	 }

	    }

	    public static function setProxy($function, $proxy) {
	    	static::$proxies[$proxy] = $function;
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
	    	 $name = static::getProxy($name);
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
			  
			  if(!is_object(static::$instance)) {
			  	return null;
			  }

			//  var_dump(static::$instance,$name);
	        return call_user_func_array(array(static::$instance,$name), array_values($arguments));


	    }

	}

?>
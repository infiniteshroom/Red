<?php
	/* this class contains all desmond objects used within the application, these acts as proxy objects to 
	various elements of framework - allowing anyone to override them at any point within the application */
	
	class Language extends DesmondObject {
		protected static $instance;
	}
	class Session extends DesmondObject {
		protected static $instance;
	}
	class HTTPResponse extends DesmondObject {
		protected static $instance;
	}

	class HTTPRequest extends DesmondObject {
		protected static $instance;
	}

	Class Database extends DesmondObject {
		protected static $instance;
	}

	Class Template extends DesmondObject {
		protected static $instance;
	}

	Class Router extends DesmondObject {
		protected static $instance;
	}

	class Application extends DesmondObject {
		protected static $instance;
	}

	Class ModulesLoader extends DesmondObject {
		protected static $instance;
	}
?>

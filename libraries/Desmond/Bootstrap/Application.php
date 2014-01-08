<?php

class DesmondApplication {

	private $settings = array();
	private $path = array();
	private $mode = 'app';

	public function SetMode($mode) {
		$this->mode = $mode;
	}

	public function Start() {

 		/* get core paths */
 		$this->path = include('paths.php');

 		/* get app paths */
		$this->path = array_merge($this->path, include($this->path['app'] . '/paths.php'));

		/* get app settings */
		$this->settings['app'] = include($this->path['config'] . 'App.php');
		$this->settings['session'] = include($this->path['config'] . 'Session.php');
		$this->settings['datastores'] = include($this->path['config'] . 'Datastores.php');


		/* if mode is cli we need to do some different things */
		if($this->mode == 'cli') {
			/* we need to include classes, but don't need controllers or error handling */
			Application::Import('Desmond::Exceptions::*');
			Application::Import('Desmond::Template::*');
			Application::Import('Desmond::Database::DBAL::*');
			Application::Import('Desmond::Database::DBAL::Database.php');
			Application::Import('Desmond::Database::DBAL::Query::*');
			Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');
			Application::Import('Desmond::Database::ORM::*');
			Application::Import('Models');

			/* let's activate the redinterpeter */
			Application::Import('Desmond::Interpeter::*');

			$desmond_interp = new DesmondInterpeter();
			echo $desmond_interp->Run();

		}

		/* else load our usual app classes */
		else {

			/* setup whoops error handling */

			Application::Import('Desmond::Exceptions::*');
			Application::Import('Whoops::Run.php');
			Application::Import('Whoops::Handler::HandlerInterface.php');
			Application::Import('Whoops::Handler::Handler.php');
			Application::Import('Whoops::Handler::PrettyPageHandler.php');
			Application::Import('Whoops::Handler::JsonResponseHandler.php');
			Application::Import('Whoops::Exception::ErrorException.php');
			Application::Import('Whoops::Exception::Inspector.php');
			Application::Import('Whoops::Exception::Frame.php');
			Application::Import('Whoops::Exception::FrameCollection.php');
			Application::Import('Kint::Kint.class.php');

			$run     = new \Whoops\Run;
			$handler = new \Whoops\Handler\PrettyPageHandler;
			$JsonHandler = new \Whoops\Handler\JsonResponseHandler;
			 
			$run->pushHandler($JsonHandler);
			$run->pushHandler($handler);

			$handler->addDataTable('Red Framework - Application', $this->settings['app']);
			$handler->addDataTable('Red Framework - Paths', $this->path);
			$handler->addDataTable('Red Framework - Other', array(
				'Namespace' => $this->CurrentNamespace(),
			));

			$run->register();

			/* include template engine and set twig as current engine */
			Application::Import('Desmond::Template::*');
			Application::Import('Desmond::Database::DBAL::*');
			Application::Import('Desmond::Database::DBAL::Database.php');
			Application::Import('Desmond::Database::DBAL::Query::*');
			Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');
			Application::Import('Desmond::Database::ORM::*');
			Application::Import('Models');

			/* setup session manager */

			if(Application::Setting('session::type') == 'memory') {
				Application::Import('Desmond::Session::Memory::*');

				Session::override(new MemorySession());
			}

			else {
				Application::Import('Desmond::Session::Fallback::*');
				Session::override(new FallbackSession());
			}


			Template::override(new TwigTemplate());
			Database::override(new DesmondDatabase());

			/* start the router and load routing rules for the app */
			Application::Import('Desmond::HTTP::Router::*');
			Application::Import('Desmond::HTTP::Request::*');
			Application::Import('Desmond::HTTP::Response::*');

			/* setup language manager */
			Application::Import('Desmond::Language::Loader::*');

			Language::override(new DesmondLanguage());	

			/* setup the default request and response objects */
			HTTPRequest::override(new DesmondRequest());
			HTTPResponse::override(new DesmondResponse());

			Router::override(new DesmondRouter());


			/* include routes */
			include($this->path['app'] . 'routes.php');

			Router::Process();
		}

	}



	public function Import($namespace) {
	 	$lastchar = $namespace[strlen($namespace)-1];
	   
	   if($lastchar == "*") 
	   {
			ModulesLoader::LoadAll($namespace);
		  
		   
	   }
	   
	   else if(strpos($namespace, '.php')) {
		   ModulesLoader::LoadSingle($namespace);
	   }
	   
	   else {
		   ModulesLoader::LoadCollection($namespace);
		   
	   }
	
	}

	public function Setting($name, $value=null) {

		/* breakup setting */

		if(strstr($name, '::') != "") {
			$setting_parts = explode('::', $name);


			if($value == null) {



				return $this->settings[$setting_parts[0]][$setting_parts[1]];
			}

			else {
				$this->settings[$setting_parts[0]][$setting_parts[1]] = $value;
			}
		}

		else {


			if($value == null) {
				return $this->settings[$name];
			}

			else {
				$this->settings[$name] = $value;
			}
		}

	}

	public function Path($name) {
		return $this->path[$name];
	}

	public function CurrentNamespace() {
		/* work out current namespace and return */

		$reflector = new ReflectionClass(get_class($this));
		$file = $reflector->getFileName();

		$namespace = str_replace($this->path['libraries'], '', $file);
		$namespace = str_replace('/', '::', dirname($namespace));

		return $namespace;

	}
}

?>
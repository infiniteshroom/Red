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

 		/* generate web path */
 		$docpath = $_SERVER['DOCUMENT_ROOT'];
		$sitepath = (dirname($_SERVER['SCRIPT_NAME']) == '/' || dirname($_SERVER['SCRIPT_NAME']) == '\\') ? '' : dirname($_SERVER['SCRIPT_NAME']);

		/* Fallback for missing PATH_INFO */
		if(!isset($_SERVER['PATH_INFO'])) {
		    $_SERVER['PATH_INFO'] = '/';		
		}

		$this->path['web'] = $sitepath;
		

 		/* get app paths */
		$this->path = array_merge($this->path, include($this->path['app'] . '/paths.php'));

		/* get app settings */
		$this->settings['app'] = include($this->path['config'] . 'App.php');
		$this->settings['auth'] = include($this->path['config'] . 'Auth.php');
		$this->settings['session'] = include($this->path['config'] . 'Session.php');
		$this->settings['datastores'] = include($this->path['config'] . 'Datastores.php');
		$this->settings['mail'] = include($this->path['config'] . 'Mail.php');
		$this->settings['permissions'] = include($this->path['config'] . 'Permissions.php');

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
			Application::Import('Desmond::Controller::*');
			Application::Import('Desmond::Template::*');
			Application::Import('Desmond::Database::DBAL::*');
			Application::Import('Desmond::Database::DBAL::Database.php');
			Application::Import('Desmond::Database::DBAL::Query::*');
			Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');
			Application::Import('Desmond::Database::ORM::*');
			Application::Import('Desmond::Auth::Password::Password.php');
			Application::Import('Desmond::Auth::Auth::Auth.php');
			Application::Import('Desmond::Mail::Mail.php');
			Application::Import('Models');

			/* include base element for templates */
			Application::Import('Desmond::Element::Element.php');

			/* include all elements */
			$elements = glob(Application::path('elements') . '*' , GLOB_ONLYDIR);

			foreach($elements as $element) {
				include($element . '/logic.php');
			}

			
			/* start the router and load routing rules for the app */
			Application::Import('Desmond::HTTP::Router::*');
			Application::Import('Desmond::HTTP::Request::*');
			Application::Import('Desmond::HTTP::Response::*');

			/* setup the default request and response objects */
			HTTPRequest::override(new DesmondRequest());
			HTTPResponse::override(new DesmondResponse());



			/* setup session manager */

			if(Application::Setting('session::type') == 'memory') {
				Application::Import('Desmond::Session::Memory::*');

				Session::override(new MemorySession());
			}

			else if(Application::Setting('session::type') == 'database') {
				Application::Import('Desmond::Session::Database::*');

				Session::override(new DatabaseSession());
			}


			else {
				Application::Import('Desmond::Session::Fallback::*');
				Session::override(new FallbackSession());
			}

			Template::override(new TwigTemplate());
			Database::override(new DesmondDatabase());

			/* setup language manager */
			Application::Import('Desmond::Language::Loader::*');

			Language::override(new DesmondLanguage());	

			Router::override(new DesmondRouter());

			/* setup password and auth system */
			Password::override(new DesmondPassword());
			Auth::override(new DesmondAuth());

			/* mail desmond */
			Mail::override(new DesmondMail());

			/* load plugins */
			$this->loadPlugins();


			/* include routes */
			include($this->path['app'] . 'routes.php');

			Router::Process();
		}

	}

	private function loadPlugins() {
		/* we load all plugins into the current scope */

			/* include all elements */
			$plugins = glob(Application::path('plugins') . '*' , GLOB_ONLYDIR);

			foreach($plugins as $plugin) {
				/* read metadata */

				$meta = json_decode(file_get_contents($plugin . '/metadata.json'));

				/* set view path to include plugin */
				Template::addPath($plugin . '/views/');


				/* include all config, controllers, language(?), models and set view path to include plugin */
				include($plugin . '/desmonds.php');
				
				/* include all config files */

				$configs = glob($plugin . '/config/*');

				foreach($configs as $config) {

						if(basename($config) != 'config') {

							if(strstr($config, 'App.php')) {
								$this->settings['app'] += include($config);
							}

							else if(strstr($config, 'Auth.php')) {
								$this->settings['auth'] += include($config);
							}

							else if(strstr($config, 'Session.php')) {
								$this->settings['session'] += include($config);
							}

							else if(strstr($config, 'Datastores.php')) {
								$this->settings['datastores'] += include($config);
							}					

							else if(strstr($config, 'Mail.php')) {
								$this->settings['mail'] += include($config);
							}
							else {

								/* custom config file - decide on this later */
								//$this->settings[];
							}

						include($config);
					}
				}




				/* include all controllers */
				$controllers = glob($plugin . '/controllers/*');

				foreach($controllers as $controller) {
					if(basename($controller) != 'controllers') {


						include($controller);
					}
				}				

				/* include all modals */
				$models = glob($plugin . '/models/*');

				foreach($models as $model) {

					
					if(basename($model) != 'models') {
						include($model);
					}
				}

				/* include routes */
				include($plugin . '/routes.php');


				/* start the plugin */
				include($plugin . '/start.php');

				/* create class */
				$plugin_class_name = 'plugin_' . $meta->name;

				$plugin_obj = new $plugin_class_name();
				$plugin_obj->Start();


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

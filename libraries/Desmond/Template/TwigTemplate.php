<?php
/* import all twig files */
Application::Import('Twig::Autoloader.php');
Twig_Autoloader::register();

class TwigTemplate implements ITemplate {
		public function Render($view, $vars=array(), $custompath=null) {

			if($custompath == null) {
				/* build up view path */
				$path = Application::Path('views');
				
			}

			else {
				$path = $custompath;
			}


			$view = str_replace("::", "/", $view);

			$loader = new Twig_Loader_Filesystem($path);
			$twig = new Twig_Environment($loader, array(
			    'cache' => Application::Path('temp'). 'cache/templates',
			    'auto_reload' => true,
			    'debug' => true,
			));

			$twig->addGlobal('app', Application::instance());
			$twig->addGlobal('lang', Language::instance());
			$twig->addGlobal('request', HTTPRequest::instance());
			$twig->addGlobal('response', HTTPResponse::instance());
			$twig->addGlobal('session', Session::instance());
			$twig->addGlobal('auth', Auth::instance());

			/* include all elements */
			$elements = glob(Application::path('elements') . '*' , GLOB_ONLYDIR);

			foreach($elements as $element) {
				/* setup default object */
				
				$element_raw = basename($element);
				$element_parts = explode('@', $element_raw);
				$element_name = $element_parts[0]. 'Element';
				

				$twig->addGlobal($element_parts[0], new $element_name());

			}

			$twig->addExtension(new Twig_Extension_Debug());

			echo $twig->render($view, $vars);

		}

		public function Buffer($view, $vars=array(), $custompath=null) {

			if($custompath == null) {
				/* build up view path */
				$path = Application::Path('views');
				
			}

			else {
				$path = $custompath;
			}

			$view = str_replace("::", "/", $view);

			$loader = new Twig_Loader_Filesystem($path);
			$twig = new Twig_Environment($loader, array(
			    'cache' => Application::Path('temp'). 'cache/templates',
			    'debug' => true,
			));
			
			$twig->addGlobal('app', Application::instance());
			$twig->addGlobal('lang', Language::instance());
			$twig->addGlobal('request', HTTPRequest::instance());
			$twig->addGlobal('response', HTTPResponse::instance());
			$twig->addGlobal('session', Session::instance());
			$twig->addGlobal('auth', Auth::instance());

			/* include all elements */
			$elements = glob(Application::path('elements') . '*' , GLOB_ONLYDIR);

			foreach($elements as $element) {
				/* setup default object */
				
				$element_raw = basename($element);
				$element_parts = explode('@', $element_raw);
				$element_name = $element_parts[0]. 'Element';
				

				$twig->addGlobal($element_name, new $element_name());

			}


			$twig->addExtension(new Twig_Extension_Debug());

			return $twig->render($view, $vars);
		}
	}
?>
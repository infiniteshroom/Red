<?php
/* import all twig files */
Application::Import('Twig::Autoloader.php');
Twig_Autoloader::register();

class TwigTemplate implements ITemplate {
		public function Render($view, $vars=array()) {
			/* build up view path */
			$path = Application::Path('views');
			$view = str_replace("::", "/", $view);

			$loader = new Twig_Loader_Filesystem($path);
			$twig = new Twig_Environment($loader, array(
			    'cache' => Application::Path('temp'). 'cache/templates',
			    'auto_reload' => true,
			    'debug' => true,
			));

			$twig->addGlobal('lang', Language::instance());
			$twig->addGlobal('request', HTTPRequest::instance());
			$twig->addGlobal('response', HTTPResponse::instance());
			$twig->addGlobal('session', Session::instance());

			$twig->addExtension(new Twig_Extension_Debug());

			echo $twig->render($view, $vars);

		}

		public function Buffer($view, $vars=array()) {
			/* build up view path */
			$path = Application::Path('views');
			$view = str_replace("::", "/", $view);

			$loader = new Twig_Loader_Filesystem($path);
			$twig = new Twig_Environment($loader, array(
			    'cache' => Application::Path('temp'). 'cache/templates',
			    'debug' => true,
			));

			$twig->addGlobal('lang', Language::instance());
			$twig->addGlobal('request', HTTPRequest::instance());
			$twig->addGlobal('response', HTTPResponse::instance());
			$twig->addGlobal('session', Session::instance());

			$twig->addExtension(new Twig_Extension_Debug());

			return $twig->render($view, $vars);
		}
	}
?>
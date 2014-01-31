<?php

class DesmondRouterMethods {
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
}

class DesmondRouter {


	public $routes = array();

	public function Process($request=null) {
		/* no request given check pathinfo for the request */

		foreach($this->routes as $route) {

			if(isset($_SERVER['PATH_INFO'])) {
				$request = $_SERVER['PATH_INFO'];
			}


			if($route['type'] != 'function') {
				if($request != '/') {
					$parts_request = explode('/', $_SERVER['PATH_INFO']);
					$request = $parts_request[1];
				}

			}

			if($route['route'] == $request || $route['route'] . '/' == $request || $route['route'] == '/' . $request) {
				if($route['type'] == 'function') {

					if($route['method'] == 'all') {
						$function = $route['func'];
						$function();


					}

					else if(strtoupper($route['method']) == DesmondRouterMethods::HTTP_GET &&  $_SERVER['REQUEST_METHOD'] == 'GET')
					{
						$function = $route['func'];
						$function();
					}

					else if(strtoupper($route['method']) == DesmondRouterMethods::HTTP_POST &&  $_SERVER['REQUEST_METHOD'] == 'POST')
					{
						$function = $route['func'];
						$function();
					}

				}

				if($route['type'] == 'controller') {
					Application::import('Controller::' . $route['controller'] . '.php');

					/* set controller var in request object */
					HTTPrequest::Controller(str_replace('Controller', '', $route['controller']));
					$controller_class = $route['controller'];
					$controller = new $controller_class();
					$controller->Init();
					$controller->render();
				}
			}
		}
	}

	public function Map($route, $item, $method='all') {
		if(is_callable($item)) {
			$this->routes[] = array(
			'type' => 'function',
				'route' => $route,
				'func' => $item,
				'method' => $method,
			);
		}

		else {
			$this->routes[] = array(
				'type' => 'controller',
				'route' => $route,
				'controller' => $item,
				'method' => $method,
			);
		}
	}

	public function Missing() {

	}
}

?>
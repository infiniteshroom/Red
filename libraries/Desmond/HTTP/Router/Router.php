<?php

Application::import('Desmond::HTTP::Router::Query.php');
class DesmondRouterMethods {
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
}

class DesmondRouter {


	public $routes = array();
	public $missing = array();

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

			if($route['route'] == $request || $route['route'] . '/' == $request || $route['route'] == '/' . $request 
				|| (0 === strpos($request, $route['route']) && $route['route'] != '/') ) {
				if($route['type'] == 'function') {

					if($route['method'] == 'all') {
						$function = $route['func'];
						Logger::Write("Route called: ANY {$route['route']}, Function", 'information'); 
						$function();


						
					}

					else if(strtoupper($route['method']) == DesmondRouterMethods::HTTP_GET &&  $_SERVER['REQUEST_METHOD'] == 'GET')
					{
						$function = $route['func'];
						Logger::Write("Route called: GET {$route['route']}, Function", 'information'); 
						$function();
						
					}

					else if(strtoupper($route['method']) == DesmondRouterMethods::HTTP_POST &&  $_SERVER['REQUEST_METHOD'] == 'POST')
					{
						$function = $route['func'];
						Logger::Write("Route called: POST {$route['route']}, Function", 'information');
						$function();
						 
					}

				}

				if($route['type'] == 'controller') {

			

					$controller_parts = explode('::', $route['controller']);
					Application::import('Controller::' . $controller_parts[0] . '.php');

					/* set controller var in request object */
					HTTPrequest::Controller(str_replace('Controller', '', $controller_parts[0]));
					$controller_class = $controller_parts[0];
					$controller = new $controller_class();

					Logger::Write("Route called: {$route['route']}, Controller: {$controller_class}", 'information'); 

					$controller->Init();


					if(isset($controller_parts[1])) {
						$action = $controller_parts[1];

						$result = $controller->$action();

						$controller->setActionContent($result);

						$controller->request->Action(explode('_', $controller_parts[1])[1]);
					}

					else {
						$controller->ProcessActions();
					}

					

					$controller->render();
				}
			}
		}

		/* if we got here no route is configured for this page */

		if(isset($this->missing['404'])) {
			if($this->missing['404']['type'] == 'function') {
				$function = $this->missing['404']['func'];
				Logger::Write("Route called: 404, attempted route: " . $route, 'information'); 
				$function();

			}

			else {
			Application::import('Controller::' . $this->missing['404']['controller'] . '.php');

			/* set controller var in request object */
			HTTPrequest::Controller(str_replace('Controller', '', $this->missing['404']['controller']));
			$controller_class = $this->missing['404']['controller'];
			$controller = new $controller_class();
			Logger::Write("Route called: 404, Controller: " . $controller, 'information'); 
			$controller->Init();
			$controller->render();

			}
		}
	}

	public function Route($route) {
		/* setup a router query */
		$query = new RouterQuery($route);

		return $query;
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

	public function Missing($item, $method='all') {
		if(is_callable($item)) {
			$this->missing['404'] = array(
			'type' => 'function',
				'func' => $item,
				'method' => $method,
			);
		}

		else {
			$this->missing['404'] = array(
				'type' => 'controller',
				'controller' => $item,
				'method' => $method,
			);
		}
	}
}

?>
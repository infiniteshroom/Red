<?php
	Application::import('Desmond::HTTP::Router::IRouterQuery.php');

	class RouterQuery implements IRouterQuery {

		private $query = array();
		private $route = '';

		function __construct($route = '/') {
			$this->route = $route;
		}

		public function action($name) {
			$this->query['action'] = $name;
			return $this;
		}

		public function host($host) {
			$this->query['host'] = $host;
			return $this;
		}
		public function parameter($name, $type) {
			$this->query['parameter'][] = array($name, $type);
			return $this;

		}
		public function controller($name) {
			$this->query['controller'] = $name;
			return $this;

		}
		public function method($verb) {
			$this->query['method'] = strtoupper($verb);
			return $this;

		}
		public function permission($group, $mode='allow') {
			$this->query['permission'][] = array($group, $mode);
			return $this;
		}

		/* function must return bool */
		public function where($function) {
			$this->query['where'] = $function;
			return $this;
		}

		public function add() {
			/* process route for adding, if we fail a check we don't add the route */

			if(isset($this->query['host'])) {

				$host = $this->query['host'];
				/* check the host given is valid */
				if(gethostname() != $host) {
					return null;
				}
			}

			/* next check method */
			if(isset($this->query['method'])) {
				$method = $this->query['method'];

				if($method != $_SERVER['REQUEST_METHOD']) {
					return null;
				}
			}

			/* check our where function */
			if(isset($this->query['where'])) {
				$result = $this->query['where'];

				if(!$result) {
					return null;
				}
			}

			/* if we made it this far, the route can be added */

			if(!isset($method)) {
				$method = 'any';
			}

			if(isset($action)) {
				Router::Map($this->route, $this->query['controller'] . '@' . $this->query['action'], $method);
			}

			else {
				Router::Map($this->route, $this->query['controller'], $method);
			}
		}
	}
?>
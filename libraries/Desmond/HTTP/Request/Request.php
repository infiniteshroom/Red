<?php

Application::Import('Desmond::HTTP::Request::IRequest.php');
	class DesmondRequest implements IDesmondRequest {
		private $request_headers = null;
		private $controller = null;
		private $action = null;
		private $router_request = null;

		public function __construct() {

			/* setup headers */
			if (!function_exists('getallheaders')) {
			   foreach ($_SERVER as $name => $value) {
			      /* RFC2616 (HTTP/1.1) defines header fields as case-insensitive entities. */
			      if (strtolower(substr($name, 0, 5)) == 'http_') {
			         $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			      }
			   }
			   $this->request_headers=$headers;
			} else {
			   $this->request_headers = getallheaders();
			}


			/* let's set request paths */
			$path_parts = explode('/', $_SERVER['PATH_INFO']);

			foreach($path_parts as $key => $value) {
				if($path_parts[$key] == '') {
					unset($path_parts[$key]);
				}
			}

			$path_parts = array_values($path_parts);

			/* check if a controller has been set */
			if(isset($path_parts[0])) {


				if($path_parts[0] != '') {
					$this->controller = $path_parts[0];
				}
			}

			/* check if a action has been set */
			if(isset($path_parts[1])) {
				if($path_parts[1] != '') {
					$this->action = $path_parts[1];
				}
			}
		}

		public function Cookie($name) {
			if(isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}

			else {
				return "";
			}
		}

		public function Form($name) {
			if(isset($_POST[$name])) {
				return $_POST[$name];
			}

			else {
				return "";
			}
		}

		public function QueryString($name) {
			if(isset($_GET[$name])) {
				return $_GET[$name];
			}

			else {
				return "";
			}
		}

		public function Files($name) {
			if(isset($_FILES[$name])) {
				return $_FILES[$name];
			}

			else {
				return "";
			}
		}

		public function Action($name = null) {
			if($name == null) {
				return $this->action;
			}

			else {
				$this->action = $name;
			}
		}

		public function RouterRequest($name = null) {
			if($name == null) {
				return $this->router_request;
			}

			else {
				$this->router_request = $name;
			}
		}

		public function Controller($name = null) {
			if($name == null) {
				return $this->controller;
			}

			else {
				$this->controller = $name;
			}
		}

		public function HttpMethod() {
			return $_SERVER['REQUEST_METHOD'];
		}

		public function Header($name) {

			if(isset($this->request_headers[$name])) {
				return $this->request_headers[$name];
			}

			else {
				return "";
			}
		}

		public function isLocal() {
			if($this->UserHostIP() == 'localhost' || $this->UserHostIP() == '127.0.0.1') {
				return true;
			}

			else {
				return false;
			}

		}

		public function UserAgent() {
			return $this->request_headers['User-Agent'];
		}

		public function UserHostIP() {
			return $_SERVER['REMOTE_ADDR'];

		}

		public function UrlReferrer() {
			return $_SERVER['HTTP_REFERER'];
		}
	}
?>
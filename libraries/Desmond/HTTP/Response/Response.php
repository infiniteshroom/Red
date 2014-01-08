<?php

Application::Import('Desmond::HTTP::Response::IResponse.php');
	class DesmondResponse implements IDesmondResponse {
		private $headers = array();
		private $content = "";

		public function SetContentType($type) {
			$this->headers['Content-type'] = $type; 
		}
		public function SetHeader($header, $value) {
			$this->headers[$header] = $value;
		}
		public function SetContent($content) {
			$this->content = $content;
		}

		public function SetCookie($name, $value, $expiry) {
			setcookie($name, $value, $expiry, "/");
		}

		public function GetHeaders() {
			return $this->headers;
		}

		public function GetContent() {
			return $this->content;
		}
	}
?>
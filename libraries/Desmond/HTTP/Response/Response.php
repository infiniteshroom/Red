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

		public function Redirect($location, $data=array()) {
			/* controller::action or pure location */

			/* we set content here, to avoid view being rendered. */
			$this->SetContent("redirect");

			foreach($data as $key => $value) {
				if(is_object($value)) {
					$data[$key] = base64_encode(serialize($data[$key]));
				}
			}
			if(strstr($location, '::') === false) {
				$this->SetHeader('Location', Application::Path('web') . $location);
			}

			/* redirect and carry over some template variables */
			if(count($data) > 0) {
				Session::Set('redirect_data', $data);
			}
			
		}
	}
?>
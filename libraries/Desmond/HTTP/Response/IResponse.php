<?php
	Interface IDesmondResponse {

		public function SetContentType($type);
		public function SetHeader($header, $value);
		public function SetContent($content);
		public function SetCookie($name, $value, $expiry);
		public function GetHeaders();
		public function GetContent();
	}
?>
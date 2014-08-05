<?php
	Interface IDesmondRequest {
		public function Cookie($name);
		public function Form($name);
		public function QueryString($name);
		public function Files($name);
		public function Action($name=null);
		public function Controller($name=null);
		public function RouterRequest($name=null);
		public function HttpMethod();
		public function Header($name);
		public function isLocal();
		public function UserAgent();
		public function UserHostIP();
		public function UrlReferrer();

	}
?>
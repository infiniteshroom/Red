<?php
	interface ITemplate {

		public function addPath($path);
		public function Render($view, $vars=array(), $custompath=null);
		public function Buffer($view, $vars=array(), $custompath=null);
	}
?>
<?php
	interface ITemplate {
		public function Render($view, $vars=array());
		public function Buffer($view, $vars=array());
	}
?>
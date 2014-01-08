<?php
	interface IActionHandler {
		function __construct(IController $controller);
		function Process();
	}
?>
<?php

	interface IDesmondLanguage {
		function LoadFile($name);
		function Get($name);
		function Set($name, $value);

	}
?>
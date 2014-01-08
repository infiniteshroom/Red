<?php

Application::import('Desmond::Language::Loader::ILanguage.php');

	Class DesmondLanguage implements IDesmondLanguage {
		private $strings = array();

		function __construct() {
			$this->strings = include(Application::Path('language') . Application::Setting('app::Language'). '/' . 'global.php');
		}

		function LoadFile($name) {

			if(strstr($name, '::') != "") {
				$path = str_replace('::', '/', $name);

				if(file_exists(Application::Path('language') . Application::Setting('app::Language'). $path. '.php')) {

					$this->strings = include(Application::Path('language') . Application::Setting('app::Language'). $path. '.php');
				}
			}

			else {
				if(file_exists(Application::Path('language') . Application::Setting('app::Language'). '/' . $name. '.php')) {

					$this->strings = include(Application::Path('language') . Application::Setting('app::Language'). '/' . $name. '.php');

				}
			}

		}

		function Get($name) {

			if(isset($this->strings[$name])) {
				return $this->strings[$name];
			}

			else {
				return "";
			}
		}

		function Set($name, $value) {
			$this->strings[$name] = $value;

		}

	}
?>
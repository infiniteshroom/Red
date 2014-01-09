<?php
	class DesmondCommandMake {
		private $args = array();

		function __construct($args) {
			$this->args = $args;
		}

		public function Process() {
			/* get make item */

			$template = ucwords($this->args[0]);
			unset($this->args[0]);

			$template_data = file_get_contents(Application::Path('libraries') . "Desmond/Interpeter/Templates/{$template}.php");


			foreach($this->args as $key => $value) {
				$template_data = str_replace('{arg'.$key.'}',$value, $template_data);
			}

			/* get path of output file */
			$path_raw = $this->get_string_between($template_data, '@path', '@end');

			/* fix app path */
			$path = trim(str_replace("/app/", Application::Path('app') , $path_raw));

			/* remove path var */
			$template_data = trim(str_replace('@path'.$path_raw. '@end', '' , $template_data));


			file_put_contents($path . $this->args[1] . '.php', $template_data);

			$output = "Template: {$template} has been created\n";
			$output .= "File: {$path}{$this->args[1]}.php\n";

			return $output;

		}

		private function get_string_between($string, $start, $end){
		    $string = " ".$string;
		    $ini = strpos($string,$start);
		    if ($ini == 0) return "";
		    $ini += strlen($start);
		    $len = strpos($string,$end,$ini) - $ini;
		    return substr($string,$ini,$len);
		}

	}
?>
<?php
	class DesmondInterpeter {
		private $args = array();
		private $missing = false;

		function __construct() {
			$this->args = $_SERVER['argv'];

			/* check if we were given a command */
			if(count($this->args) == 1) {
				$this->missing = true;
			}

			unset($this->args[0]);
			$this->args = array_values($this->args);
		}

		public function Run() {
			/* include and run the correct command */

			if($this->missing == true) {
				return $this->RunMissing();
			}

			$command_name = strtolower($this->args[0]);
			unset($this->args[0]);
			$this->args = array_values($this->args);

			try {
				Application::Import("Desmond::Interpeter::Commands::{$command_name}.php");

				$command_class = 'DesmondCommand' . ucwords($command_name);
				$command = new $command_class($this->args);

				return $command->Process();
			}

			catch(DesmondModuleMissingException $e) {
				return $this->RunMissing();
			}

		}

		private function RunMissing() {
			Application::Import("Desmond::Interpeter::Commands::missing.php");

			$missing_command = new DesmondCommandMissing(array());
			return $missing_command->Process();
		}
	}
?>

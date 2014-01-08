<?php
	Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseQuery.php');
	Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');

	class DesmondDatabaseQuery implements IDatabaseQuery {
		private $driverobj = null;
		private $driver = null;
		private $parameters = array();
		
		public function __construct(IDatabaseConnection $conn) {
			$driverconn = $conn->GetDriverObject();
			$this->driver = $conn->GetDriverName();

			$driver_class = $this->driver . 'Query';
			$this->driverobj = new $driver_class($driverconn);

		}

		public function Execute($sql) {
			/* parse parameters */
			foreach($this->parameters as $parameter) {

				$name = $parameter['name'];
				$value = $parameter['value'];

				if($parameter['type'] == 'string') {
					$sql = str_replace("@{$name}", "'{$value}'", $sql);
				}

				else if($parameter['type'] == 'int') {
					$sql = str_replace("@{$name}", (int) $value, $sql);
				}

				else {
					$sql = str_replace("@{$name}", "{$value}", $sql);
				}
			}

			$this->driverobj->Execute($sql);
		}	

		public function FetchOne() {
			return $this->driverobj->FetchOne();
		}

		public function FetchAll() {
			return $this->driverobj->FetchAll();
		}

		public function FetchObject() {
			return $this->driverobj->FetchObject();
		}
		public function Count() {
			return $this->driverobj->Count();
		}

		public function BindParameter($name, $value, $type) {
			$this->parameters[] = array(
				'name' => $name,
				'value' => $value,
				'type' => $type,
			);

		}
	}

?>
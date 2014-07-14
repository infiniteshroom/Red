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

		private function GetParameter($name) {
			foreach($this->parameters as $parameter) {
				if($parameter['name'] == $name) {
					return $parameter;
				}
			}
		}

		public function Execute($sql) {

			/* parse parameters */
			$sql_parts = explode(' ', $sql);

			foreach($sql_parts as $sql_part) {
				if(strpos($sql_part, "@") === 0) {

					$parameter = $this->GetParameter(str_replace("@", "", $sql_part));

					$this->AddParameter($parameter['value'], $parameter['type']);

					//$tmp[] = $parameter['value'];

					$sql = str_replace($sql_part, "?", $sql);
				}
			}


			$this->driverobj->Execute($sql);

			Logger::Write("Database executed Query: " . $sql, 'information');

			$this->parameters = array();
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

		public function GetInsertID() {
			return $this->driverobj->GetInsertID();
		}

		public function Escape($input) {
			return $this->driverobj->Escape($input);
		}

		public function AddParameter($param, $type) {
			Logger::Write("Database Added Parameter: " . $param . " type: " . $type, 'information');
			return $this->driverobj->AddParameter($param, $type);
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

<?php
	Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseConnection.php');
	Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');
	Application::Import('Desmond::Database::DBAL::Drivers::SQLite::*');

	class DesmondDatabaseConnection implements IDatabaseConnection {
		private $driver = null;
		private $driverobj = null;

		public function Open($params = array()) {

			/* use drive to create correct connection object */
			$this->driver = $params['driver'];

			$driver_class = $this->driver . 'Connection';
			$this->driverobj = new $driver_class();

			$this->driverobj->Open($params);
		}

		public function GetDBConnection() {
			return $this->driverobj->conn_obj;
		}

		public function Close() {
			mysqli_close($this->driverobj->conn_obj);
		}

		public function GetDriverName() {
			return $this->driver;
		}

		public function GetDriverObject() {
			return $this->driverobj;
		}
	}
?>
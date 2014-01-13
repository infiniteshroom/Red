<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseConnection.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseConnectionException.php');

class SQLiteConnection implements IDatabaseConnection {
	private $conn_obj = null;

	public function Open($params = array()) {

		$path = $params['path'];

		try {
			$this->conn_obj = new SQLite3($path);
		}

		catch (Exception $exception) { 
			throw new DatabaseConnectionException($exception->getMessage());
		}
		 
	}

	public function GetDBConnection() {
		return $this->conn_obj;
	}

	public function Close() {
		$this->conn_obj->close();
	}
}
?>
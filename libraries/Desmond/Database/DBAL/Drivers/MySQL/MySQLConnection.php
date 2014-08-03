<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseConnection.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseConnectionException.php');

class MySQLConnection implements IDatabaseConnection {
	private $conn_obj = null;

	public function Open($params = array()) {

		$host = $params['host'];
		$database = $params['database'];
		$username = $params['username'];
		$password = $params['password'];

		$this->conn_obj = new mysqli($host,$username, $password, $database);

		if (mysqli_connect_errno()) {
			throw new DatabaseConnectionException(mysqli_connect_error());
		}
		 
	}

	public function GetDBConnection() {
		return $this->conn_obj;
	}

	public function Close() {
		mysqli_close($this->conn_obj);
	}
}
?>
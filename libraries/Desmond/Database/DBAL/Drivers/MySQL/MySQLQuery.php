<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseQuery.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseQueryException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseNoQueryException.php');

class MySQLQuery implements IDatabaseQuery {
	private $conn_obj = null;
	private $result = null;

	public function __construct(IDatabaseConnection $conn) {
		$this->conn_obj = $conn->GetDBConnection();
	}
	public function Execute($sql) {

		$this->result = mysqli_query($this->conn_obj,$sql);

		if ($this->result === false) {
  			throw new DatabaseQueryException(mysqli_error($this->conn_obj));
		}
	}	

	public function FetchOne() {

		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		return $this->result->fetch_assoc();
	}

	public function FetchAll() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		return $this->result->fetch_array();
	}

	public function FetchObject() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$results = array();

		while($result = $this->result->fetch_object()) {
			$results[] = $result;
		}

		return $results;
	}
	public function Count() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		return $this->result->num_rows;
	}

	public function GetInsertID() {
		return mysqli_insert_id($this->conn_obj);
	}

}
?>
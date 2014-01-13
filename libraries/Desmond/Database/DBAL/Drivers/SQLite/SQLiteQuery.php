<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseQuery.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseQueryException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseNoQueryException.php');

class SQLiteQuery implements IDatabaseQuery {
	private $conn_obj = null;
	private $result = null;

	public function __construct(IDatabaseConnection $conn) {
		$this->conn_obj = $conn->GetDBConnection();
	}
	public function Execute($sql) {

		$this->result = $this->conn_obj->query($sql);

		if(!$this->result) {
			throw new DatabaseQueryException($this->conn_obj->lastErrorMsg());
		}
	}	

	public function FetchOne() {

		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$records = $this->FetchAll();
		$record = $records[0];

		return $record;
	}

	public function FetchAll() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		return $this->result->fetchArray();
	}

	public function FetchObject() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$records = $this->FetchAll();
		$record = $records[0];

		$object = new stdClass();

		foreach($record as $key => $value) {
			$object->$key = $value;
		}

		return $object;
	}
	public function Count() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$results = $this->FetchAll();

		return $results['count'];
	}
}
?>
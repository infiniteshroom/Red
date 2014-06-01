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

		$record = $this->result->fetchArray();

		/* remove numeric elements */
		foreach($record as $key => $value) {
			if(!is_string($key)) {
				unset($record[$key]);
			}
		}

		return $record;
	}

	public function FetchAll() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$results = array();
		while($row=$this->result->fetchArray()){

			/* remove numeric elements */
			foreach($row as $key => $value) {
				if(!is_string($key)) {
					unset($row[$key]);
				}
			}

		   $results[] = $row;
		}

		return $results;
	}

	public function FetchObject() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		$records = $this->FetchAll();


		$objects = array();

		foreach($records as $record) {
			$object = new stdClass();
			foreach($record as $key => $value) {

				if(is_string($key)) {
					$object->$key = $value;
				}
			}

			$objects[] = $object;

		}
		return $objects;
	}
	public function Count() {
		if($this->result == null) {
			throw new DatabaseNoQueryException();
		}

		return $this->result->numColumns();
	}

	public function GetInsertID() {
		return $this->conn_obj->lastInsertRowID();
	}
}
?>
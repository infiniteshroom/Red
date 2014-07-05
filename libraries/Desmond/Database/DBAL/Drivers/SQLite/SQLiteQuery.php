<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseQuery.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseQueryException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseNoQueryException.php');

class SQLiteQuery implements IDatabaseQuery {
	private $conn_obj = null;
	private $result = null;
	private $parameters = array();

	public function __construct(IDatabaseConnection $conn) {
		$this->conn_obj = $conn->GetDBConnection();
	}
	public function Execute($sql) {

		$query = $this->conn_obj->prepare($sql);

		$query = $this->ProcessParameters($query);

		$this->result = $query->execute();

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

	public function Escape($input) {
		return sqlite_escape_string($input);
	}

	public function AddParameter($param, $type) {
		if($type == 'string') {
			$parameters = array(
				'value' => $param,
				'type' => SQLITE3_TEXT,
			);
		}

		else if($type == 'int') {
			$parameters = array(
				'value' => $param,
				'type' => SQLITE3_INTEGER,
			);
		}

		else if($type == 'float') {
			$parameters = array(
				'value' => $param,
				'type' => SQLITE3_FLOAT,
			);
		}
	}

	private function ProcessParameters($query) {
		$count = 1;
		foreach($this->parameters as $key => $value) {
			$query->bindParam($count, $value['value'], $value['type']);
			$count++;
		}

		return $query;
	}
}
?>

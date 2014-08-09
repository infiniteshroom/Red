<?php

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseQuery.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseQueryException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::DatabaseNoQueryException.php');

class MySQLQuery implements IDatabaseQuery {
	private $conn_obj = null;
	private $query = null;
	private $count = null;
	public $parameters = array();

	public function __construct(IDatabaseConnection $conn) {
		$this->conn_obj = $conn->GetDBConnection();
	}
	public function Execute($sql) {

		$this->count = 0;
		$this->query = $this->conn_obj->prepare($sql);
		
		if ($this->query === false) {
  			throw new DatabaseQueryException(mysqli_error($this->conn_obj));
		}

		$this->ProcessParameters();

		


		$this->query->execute();



		$this->query->store_result();
		$this->count = $this->query->num_rows;


		if ($this->query === false) {
  			throw new DatabaseQueryException(mysqli_error($this->conn_obj));
		}

		

	}	

	public function FetchOne() {

		if($this->query == null) {
			throw new DatabaseNoQueryException();
		}


	   $results = array();

	    $meta = $this->query->result_metadata();
	    $fields = $meta->fetch_fields();
	    foreach($fields as $field) {
	        $result[$field->name] = "";
	        $result_array[$field->name] = &$result[$field->name];
	    }

	    call_user_func_array(array($this->query, 'bind_result'), $result_array);

	    $result = null;
   	 	while($this->query->fetch()) {
	        $row = array();
	        $count = 0;

	        foreach ($result_array as $key => $value) {
	            $row[$key] = $value;

	            $count++;
	        }

	        $result = $row;
	        break;
    	}

	$this->parameters = array();
    	return $result;

	}

	public function FetchAll() {

		if($this->query == null) {
			throw new DatabaseNoQueryException();
		}

	   $results = array();

	    $meta = $this->query->result_metadata();
	    $fields = $meta->fetch_fields();
	    foreach($fields as $field) {
	        $result[$field->name] = "";
	        $result_array[$field->name] = &$result[$field->name];
	    }

	    call_user_func_array(array($this->query, 'bind_result'), $result_array);

	    $results = array();
   	 	while($this->query->fetch()) {
	        $row = array();
	        $count = 0;

	        foreach ($result_array as $key => $value) {
	        	$row[$count] = $value;
	            $row[$key] = $value;

	            $count++;
	        }

	        $results[] = $row;
    	}
$this->parameters = array();
		return $results;
	}

	public function FetchObject() {
		if($this->query == null) {
			throw new DatabaseNoQueryException();
		}

	   $results = array();

	    $meta = $this->query->result_metadata();
	    $fields = $meta->fetch_fields();
	    foreach($fields as $field) {
	        $result[$field->name] = "";
	        $result_array[$field->name] = &$result[$field->name];
	    }

	    call_user_func_array(array($this->query, 'bind_result'), $result_array);

   	 	while($this->query->fetch()) {
	        $object = new stdClass();

	        foreach ($result_array as $key => $value) {
	            $object->$key = $value;
	        }

	        $results[] = $object;
    	}

	$this->parameters = array();
		return $results;
	}
	public function Count() {
		return $this->count;
	}

	public function GetInsertID() {
		return mysqli_insert_id($this->conn_obj);
	}

	public function Escape($input) {
		return mysqli_real_escape_string($this->conn_obj, $input);
	}

	public function AddParameter($param, $type) {
		if($type == 'int') {
			$this->parameters[] = array(
				'value' => $param,
				'type' => 'i',
			);
		}

		else if($type == 'string') {
			$this->parameters[] = array(
				'value' => $param,
				'type' => 's',
			);
		}

		else if($type == 'float') {
			$this->parameters[] = array(
				'value' => $param,
				'type' => 'f',
			);
		}

	}

	private function ProcessParameters() {

		$params = array();

		foreach($this->parameters as $key => $value) {
			if(isset($params[0])) {
				$params[0] = $params[0] . $value['type'];
			}

			else {
				$params[0] = $value['type'];
			}

			$params[] = $value['value'];
		}

		    
	if(count($params) > 0) {
		$tmp = array();

        foreach($params as $key => $value) {
        	$tmp[$key] = &$params[$key];
        }


        try {
        call_user_func_array(array($this->query, 'bind_param'), $tmp);
        }

        catch(ErrorException $e) {
        	
        } 

    	}

	}

}
?>

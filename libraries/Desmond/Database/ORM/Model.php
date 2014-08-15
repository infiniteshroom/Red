<?php
Application::Import('Desmond::Database::ORM::IModel.php');
Application::Import('Valitron::Validator::Validator.php');

	class Model implements IModel {
		protected $builder = null;
		protected $table = '';
		protected $key = 'id';
		protected $datastore = 'default'; 
		protected $validators = array();
		protected $attributes = array();
		protected $relationships = array();

		private $errors = array();


		public function __construct() {
			/* bind query builder */
			$this->builder = Database::table($this->table);
		}


		public function GetTable() {
			 return $this->table;
		}

		public function GetAttributeNames() {
			return array_keys($this->attributes);
		}


		public static function Fill($data) {

			$model_name = get_called_class();
			$model = new $model_name();
			
			foreach($data as $key => $value) {
				$model->$key = $value;
			}

			return $model;
		}

		/* get relationship within ORM */
		public static function Relationship($name) {

			$model = get_called_class();
			$model_obj = new $model(false);
			
			return $model_obj->Relation($name, true);
		}


		public function Relation($name, $full = false) {

			$this->builder = Database::table($this->table);
			/* find correct relation - this currently only applies to 1-* and *-1 relationships - now allows belongs */
			$model_name = $this->relationships[$name]['model'];

			$relation_key = 'id';
			$relation_value = $this->id;

			if(isset($this->relationships[$name]['relation_key'])) {
				$attribute = $this->relationships[$name]['relation_key'];

				$relation_key = $attribute;
				$relation_value = $this->$attribute;
			}
			/* get table for join operation */
			$table = $this->GetTable();

			/* get relation table */
			$relation_model_obj = new $model_name();
			$relation_table = $relation_model_obj->GetTable();

			$model_name = get_called_class();


			$model_obj = $model_name::join(array($relation_table, $table . '.' . $relation_key, '=', $relation_table .'.'.$this->relationships[$name]['key']));
			
			if(!$full) {
				$model_obj->where(array($table .'.' . $relation_key, '=', $relation_value));
			}
			return $model_obj;
		}


		public static function All() {
			$model = get_called_class();
			$obj = new $model();
			return $obj->GetBuilder()->results();
		}

		public static function Find($value) {


			$model = get_called_class();
			$obj = new $model();
			$results = $obj->GetBuilder()->where(array($obj->key, '=', $value))->results();

			if(isset($results[0])) {
				$dbobj = $results[0];
			}

			else {
				return new $model();
			}
			
			/* assign object to the new model */
			$modelfind = new $model();

			foreach(get_object_vars($dbobj) as $key => $value) {
				$modelfind->$key = $value;
			}

			return $modelfind;
		}

		public function Create($validate = true) {

			/* process validators if they exist */
			$v = new Validator($this->attributes);

			$v->rules($this->validators);

			if($v->validate() || $validate == false) {

				$id = $this->builder->insert($this->attributes);

				$this->id = $id;
			}

			else {
				$this->errors = $v->errors();
			}

			return $this;
		}

		public function Save($validate = true) {

			/* process validators if they exist */
			$v = new Validator($this->attributes);

			$v->rules($this->validators);

			if($v->validate() || $validate == false) {

				/* if no id set, then the user actually wants a create not a save */
				if($this->id == "") {
					$this->Create($validate);
					return $this;
				}

				$this->builder->where(array($this->key, '=', $this->attributes[$this->key]));
				$this->builder->update($this->attributes);
			}

			else {
				$this->errors = $v->errors();
			}

			return $this;
		}

		public function GetErrors($format='messages') {
			if($format == 'messages') {
				return $this->errors;
			}

			elseif($format == 'inputs') {
				return array_keys($this->errors);
			}

			else if($format == 'json') {
				return json_encode($this->errors);
			}
		}

		public function Delete() {
			
			if(isset($this->attributes[$this->key])) {
				if($this->attributes[$this->key] != null) {
					$this->builder->where(array($this->key, '=', $this->attributes[$this->key]));
				}
			}


			$this->builder->delete();
		}

		public function GetBuilder() {
			return $this->builder;
		}

		public function __get($key)
	    {
	    	if(isset($this->attributes[$key])) {
	        	return $this->attributes[$key];
	    	}

	    	else {
	    		return "";
	    	}
	    }

	    public function __set($key, $value)
	    {
	        $this->attributes[$key] = $value;
	    }
	    
        public function __isset($name)
	    {
	       	if(isset($name)) {
	            return true;
	        }

	        return false;
	    }


	    public function GetJson() {
	    	return json_encode($this->attributes);
	    }


		public static function  __callStatic($method, $args) {


			$model = get_called_class();
			$obj = new $model();

  			if(count($args) != 0 && $method == 'results') {

    			if($args[0] == 'json') {
    				return call_user_func_array(array($obj->GetBuilder(), $method), $args);
    			}

    			else if($args[0] == 'one') {
    				$result = call_user_func_array(array($obj->GetBuilder(), $method), $args);

    				if(isset($this->GetBuilder()->metadata['join_table']) && $this->GetBuilder()->metadata['join_table'] != '') {
						/* we search through the relationships to find a valid model */

						$relationship = false;

						foreach($this->relationships as $relationship) {
							$model = $relationship['model'];
							$model_obj = new $model();
							
							if($model_obj->GetTable() == $this->GetBuilder()->metadata['join_table']) {
								$model_name = $model;
							}
						}

						/* so we cannot find a relationship, return stdclass results */
						if(!$relationship) {
							return $result;
						}
					}

					else {
 						$model_name = get_called_class();
 					}


    				if($result != null) {
    					return $model_name::Fill($result);
    				}

    				else {
    					return null;
    				}
    			}

    			 else if($args[0] == 'array') {
					$result = call_user_func_array(array($obj->GetBuilder(), $method), $args);

					return $result;
				}

    		}

    		else if((count($args) == 0 && $method == 'results')) {
			   $results = call_user_func_array(array($obj->GetBuilder(), $method), $args);

				$results_obj = array();

				/* we need to avoid the n+1 problem - so don't find the model, instead assign the variables we have */
				foreach($results as $result) { 

    				if(isset($obj->GetBuilder()->metadata['join_table']) && $obj->GetBuilder()->metadata['join_table'] != '') {
						/* we search through the relationships to find a valid model */

						$relationship = false;

						foreach($obj->relationships as $relationship) {
							$model = $relationship['model'];
							$model_obj = new $model();
							
							if($model_obj->GetTable() == $obj->GetBuilder()->metadata['join_table']) {
								$model_name = $model;
							}
						}

						/* so we cannot find a relationship, return stdclass results */
						if(!$relationship) {
							return $results;
						}
					}

					else {
 						$model_name = get_called_class();
 					}


 					$model_obj = new $model_name();

 					foreach(get_object_vars($result) as $key => $value) {

						$model_obj->$key = $value;
					}

					$results_obj[] = $model_obj;
				}   				

				return $results_obj;
			}

    		else {
    			$result = call_user_func_array(array($obj->GetBuilder(), $method), $args);

    			if(is_null($result)) {
					return $obj;
				}

				else if($result instanceof QueryBuilder) {
					return $obj;
				}

				else {
					return $result;
				}
			}
		}

		public function __call($method, $args)
    	{

    		if(count($args) != 0 && $method == 'results') {

    			if($args[0] == 'json') {
    				return call_user_func_array(array($this->GetBuilder(), $method), $args);
    			}

    			else if($args[0] == 'one') {

    				$result = call_user_func_array(array($this->GetBuilder(), $method), $args);

    				if(isset($this->GetBuilder()->metadata['join_table']) && $this->GetBuilder()->metadata['join_table'] != '') {
						
						$relationship = false;

						foreach($this->relationships as $relationship) {
							$model = $relationship['model'];
							$model_obj = new $model();
							
							if($model_obj->GetTable() == $this->GetBuilder()->metadata['join_table']) {
								$model_name = $model;
							}
						}

						/* so we cannot find a relationship, return stdclass results */
						if(!$relationship) {
							return $result;
						}

					}

					else {
						$model_name = get_called_class();
					}
    				if($result != null) {
    					return $model_name::Fill($result);
    				}

    				else {
    					return null;
    				}
    			}

    			else if($args[0] == 'array') {
					$result = call_user_func_array(array($this->GetBuilder(), $method), $args);

					return $result;
				}
    		}

    		else if(count($args) == 0 && $method == 'results') {
			   $results = call_user_func_array(array($this->GetBuilder(), $method), $args);

				$results_obj = array();

				/* we need to avoid the n+1 problem - so don't find the model, instead assign the variables we have */
				foreach($results as $result) { 
					
					if(isset($this->GetBuilder()->metadata['join_table']) && $this->GetBuilder()->metadata['join_table'] != '') {
						/* we search through the relationships to find a valid model */

						$relationship = false;

						foreach($this->relationships as $relationship) {
							$model = $relationship['model'];
							$model_obj = new $model();
							
							if($model_obj->GetTable() == $this->GetBuilder()->metadata['join_table']) {
								$model_name = $model;
							}
						}

						/* so we cannot find a relationship, return stdclass results */
						if(!$relationship) {
							return $results;
						}
					}

					else {
 						$model_name = get_called_class();
 					}


 					$model_obj = new $model_name();

 					foreach(get_object_vars($result) as $key => $value) {

						$model_obj->$key = $value;
					}

					$results_obj[] = $model_obj;
				}   				

				return $results_obj;
			}

    		else {
    			$result = call_user_func_array(array($this->GetBuilder(), $method), $args);

    			if(is_null($result)) {
					return $this;
				}



				else if($result instanceof QueryBuilder) {
					return $this;
				}

				else {
					return $result;
				}
			}
    	}






	}
?>


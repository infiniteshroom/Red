<?php
Application::Import('Desmond::Database::ORM::IModel.php');
	class Model implements IModel {
		protected $builder = null;
		protected $table = '';
		protected $key = 'id';
		protected $datastore = 'default'; 
		protected $attributes = array();
		protected $relationships = array();


		public function __construct() {
			/* bind query builder */
			$this->builder = Database::table($this->table);

			/* get attributes */
			$result = $this->builder->results('one');

			$cols = array_keys($result);

			foreach($cols as $col) {
				$this->attributes[$col] = '';
			}

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
		public function Relation($name) {
			/* find correct relation - this currently only applies to 1-* and *-1 relationships - now allows belongs */
			$model_name = $this->relationships[$name]['model'];

			$relation_key = $this->id;

			if(isset($this->relationships[$name]['relation_key'])) {
				$attribute = $this->relationships[$name]['relation_key'];

				$relation_key = $this->$attribute;
			}

			$model_obj = $model_name::where(array($this->relationships[$name]['key'] ,'=', $relation_key));

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

		public function Create() {
			$this->builder->insert($this->attributes);

			return $this;
		}

		public function Save() {
			$this->builder->where(array($this->key, '=', $this->attributes[$this->key]));
			$this->builder->update($this->attributes);

			return $this;
		}

		public function Delete() {
			$this->builder->where(array($this->key, '=', $this->attributes[$this->key]));
			$this->builder->delete();
		}

		public function GetBuilder() {
			return $this->builder;
		}

		public function __get($key)
	    {
	        return $this->attributes[$key];
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

    				$model_name = get_called_class();

    				return $model_name::Fill($result);
    			}

    		}

    		else if(count($args) == 0 && $method == 'results') {
			   $results = call_user_func_array(array($obj->GetBuilder(), $method), $args);

				$results_obj = array();

				/* we need to avoid the n+1 problem - so don't find the model, instead assign the variables we have */
				foreach($results as $result) { 
 					$model_name = get_called_class();

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

    				$model_name = get_called_class();

    				return $model_name::Fill($result);
    			}

    		}

    		else if(count($args) == 0 && $method == 'results') {
			   $results = call_user_func_array(array($this->GetBuilder(), $method), $args);

				$results_obj = array();

				/* we need to avoid the n+1 problem - so don't find the model, instead assign the variables we have */
				foreach($results as $result) { 
 					$model_name = get_called_class();

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
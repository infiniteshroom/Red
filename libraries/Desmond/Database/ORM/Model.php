<?php
Application::Import('Desmond::Database::ORM::IModel.php');
	class Model implements IModel {
		protected $builder = null;
		protected $table = '';
		protected $key = 'id';
		protected $datastore = 'default'; 
		protected $attributes = array(); 


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

		public static function All() {
			$model = get_called_class();
			$obj = new $model();
			return $obj->GetBuilder()->results();
		}

		public static function Find($value) {


			$model = get_called_class();
			$obj = new $model();
			$results = $obj->GetBuilder()->where(array($obj->key, '=', $value))->results();

			$dbobj = $results[0];
			
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

	    public function GetJson() {
	    	return json_encode($this->attributes);
	    }


		public static function  __callStatic($method, $args) {
			$model = get_called_class();
			$obj = new $model();
			return call_user_func_array(array($obj->GetBuilder(), $method), $args);
		}





	}
?>
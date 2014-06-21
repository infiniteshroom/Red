<?php
	Application::Import('Desmond::Database::DBAL::*');
	Application::Import('Desmond::Database::DBAL::Query::*');
	Application::Import('Desmond::Database::DBAL::Drivers::MySQL::*');

	Class DesmondDatabase {
		private $datastores = array();
		private $active = 'default';

		public function __construct() {
			$datastore_strings = Application::Setting('datastores');

			foreach($datastore_strings as $key => $value) {
				/* setup a connection for each datastore */
				
				$mycon = new DesmondDatabaseConnection();
				$mycon->Open($value);

				$this->datastores[$key] = $mycon;
			}
		}

		public function __call($method, $args) {
			$builder = new QueryBuilder($this->datastores[$this->active]);
			return call_user_func_array(array($builder, $method), $args);
		}

		public function SetActive($datastore) {
			$this->active = $datastore;

			return $this;
		}

		public function GetActiveConnection() {
			return $this->datastores[$this->active];
		}

	}
?>
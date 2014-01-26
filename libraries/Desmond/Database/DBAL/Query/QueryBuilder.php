<?php

Application::Import('Desmond::Database::DBAL::Query::IQueryBuilder.php');
Application::Import('Desmond::Database::DBAL::Exceptions::QueryBuilderNoTableException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::QueryBuilderWhereOperatorException.php');



	Class QueryBuilder implements IQueryBuilder {

		private $sql = "";
		private $conn = null;
		private $table = "";
		private $orderstring = "";

		public $strings = array(
			'select' => 'SELECT {columns} FROM {table}',
			'selectall' => 'SELECT * FROM {table}',
			'where' => 'WHERE {column} {operator} {value}',
			'and' => 'AND {column} {operator} {value}',
			'empty' => 'TRUNCATE {table}',
			'insert' => 'INSERT INTO {table} ({cols}) VALUES ({values})',
			'update' => 'UPDATE {table} SET {values}',
			'orderby' => 'ORDER BY {col} {order}',
			'orderbymore' => ', {col} {order}',
		);

		public $where_operators = array(
			'equals' => '=',
			'greater'=> '>',
			'less' => '<',
			'lessequal' => '<=',
			'greaterequal' => '>=',
			'notequal' => '!=',
			'like' => 'LIKE',
		);

		function __construct(IDatabaseConnection $conn) {
			$this->conn = $conn;
		}
		
		//builder methods
		public function table($name) {						
			$this->table = $name;
			$sql = $this->ProcessString('selectall', array('table' => $this->table));
			$this->sql .= $sql;

			return $this;
		}

		public function truncate() {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$sql = $this->ProcessString('empty', array(
			'table' => $this->table,
			));

			$statement = new DesmondDatabaseQuery($this->conn);

			$statement->Execute($this->sql);
		}
		public function where($filter = array()) {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			/* check if this is the first where statement */

			$query_type = '';

			if(strstr($this->sql, 'WHERE') === false) {
				$query_type = 'where';
			}

			else {
				$query_type = 'and';
			}

			$sql = $this->ProcessString($query_type, array(
				'column' => $filter[0],
				'operator' => $filter[1],
				'value' => $this->ProcessParamaterType($filter[2]), 
				));

			if(!$this->isWhereOperator($filter[1])) {
				throw new QueryBuilderWhereOperatorException("SQL Where operator unknown '" . $filter[1] . "' {$sql}");
			}


			$this->sql .= ' ' .$sql;

			return $this;
		}

		public function columns($cols = array()) {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			/* replace old select * with the need cols */

			$cols_string = implode(',', $cols);

			$this->sql = str_replace('SELECT *', 'SELECT ' . $cols_string, $this->sql);

			return $this;


		}

		public function orderby($col, $order) {

			if($this->orderstring == '') {
				$this->orderstring = $this->ProcessString('orderby', array(
				'col' => $col,
				'order' => $order,
				));
			}

			else {
				$this->orderstring .= $this->ProcessString('orderbymore', array(
				'col' => $col,
				'order' => $order,
				));
			}

			return $this;
		}

		public function limit($amount, $offset=0) {

		}



		//final methods
		public function results($type = 'object') {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			

			$statement = new DesmondDatabaseQuery($this->conn);

			/* add on order statements if exists */

			$this->sql .= ' ' . $this->orderstring;
			$statement->Execute($this->sql);


			if($type == 'object') {
				return $statement->FetchObject();
			}

			else if($type == 'array') {
				return $statement->FetchAll();
			}

			else if($type == 'one') {
				return $statement->FetchOne();
			}

			else if($type == 'json') {
			 	return json_encode($statement->FetchObject());
			}


		}
		public function count() {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$statement = new DesmondDatabaseQuery($this->conn);

			$statement->Execute($this->sql);

			$count = $statement->Count();

			if($count == null) {
				$count = 0;
			}

			return $count;
		}
		public function delete() {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			/* find offset of from statement */
			$from_pos = strpos($this->sql, 'FROM');
			$this->sql = str_replace(substr($this->sql, 0, $from_pos - 1), 'DELETE', $this->sql);

			$statement = new DesmondDatabaseQuery($this->conn);

			$statement->Execute($this->sql);


		}

		public function distinct() {
			
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$this->sql = str_replace('SELECT', 'SELECT DISTINCT', $this->sql);

			return $this;
		}

		public function insert($data = array()) {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$cols = array_keys($data);

			$values = array_values($data);

			foreach($values as $key => $value) {
				$values[$key] = $this->ProcessParamaterType($value);
			}

			$sql = $this->ProcessString('insert', array(
			'table' => $this->table,
			'cols' => implode(',', $cols),
			'values' => implode(',', $values), 
			));
			
			$statement = new DesmondDatabaseQuery($this->conn);
			$statement->Execute($sql);



 		}


		public function update($data = array()) {
			$cols = array_keys($data);
			$values = array_values($data);

			$update_strings = array();

			foreach($cols as $key => $value) {
				$update_strings[$key] = $value . ' = ' . $this->ProcessParamaterType($values[$key]);
			}

			$update_sql = implode(',', $update_strings);

			/*remove select code from current sql and add update code */
			$pos_select = strpos($this->sql, "FROM {$this->table}");

			$select_length = strlen("FROM {$this->table}");

			$sql = substr($this->sql, $pos_select + $select_length);

			$update_sql = $this->ProcessString('update', array(
				'table' => $this->table, 
				'values' => $update_sql,
				));

			$statement = new DesmondDatabaseQuery($this->conn);
			$statement->Execute($update_sql . ' '. $sql);

			return $this;
		}

		public function raw($sql) {
			$this->sql .= $sql;

			return $this;
		}
 
		public function sql() {
			return $this->sql;
		}

		private function ProcessString($string, $vars) {

			$string = $this->strings[$string];
			foreach($vars as $key => $value) {
				$string = str_replace('{' . $key . '}', $value, $string);
			}

			return $string;
		}

		private function ProcessParamaterType($parameter) {

			if(is_int($parameter)) {
				return (int) $parameter;
			}

			else if(is_bool($parameter)) {
				if(in_array($string, array("true", "false", "1", "0", "yes", "no"))) {
					return (bool) $parameter;
				}
			}
			else if(is_string($parameter) && $parameter != '') {
				return "'" . $parameter . "'";
			}

			else {
				return "null";
			}
		}

		private function isWhereOperator($operator) {
			$operators = array_values($this->where_operators);

			if(!in_array($operator, $operators)) {
				return false;
			}

			else {
				return true;
			}
		}
 	}
?>
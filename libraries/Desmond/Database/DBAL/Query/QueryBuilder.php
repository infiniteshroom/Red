<?php

Application::Import('Desmond::Database::DBAL::Query::IQueryBuilder.php');
Application::Import('Desmond::Database::DBAL::Exceptions::QueryBuilderNoTableException.php');
Application::Import('Desmond::Database::DBAL::Exceptions::QueryBuilderWhereOperatorException.php');



	Class QueryBuilder implements IQueryBuilder {

		private $sql = "";
		private $conn = null;
		private $statement = null;
		private $table = "";
		private $orderstring = "";
		private $limitstring = "";
		
		public $metadata = array();

		public $strings = array(
			'select' => 'SELECT @columes FROM @table',
			'selectall' => 'SELECT * FROM @table',
			'where' => 'WHERE @column @operator @value',
			'and' => 'AND @column @operator @value',
			'empty' => 'TRUNCATE @table',
			'in' => 'WHERE @col IN (@values)',
			'insert' => 'INSERT INTO @table (@cols) VALUES (@values)',
			'update' => 'UPDATE @table SET @values',
			'groupby' => 'GROUP BY @col',
			'orderby' => 'ORDER BY @col @order',
			'orderbymore' => ', @col @order',
			'join' => 'INNER JOIN @table ON @col1 @operator @col2',
			'joinselect' => 'SELECT @filtertable.* FROM @table',
			'limit' => 'LIMIT @amount OFFSET @offset',
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
			$this->statement = new DesmondDatabaseQuery($this->conn);
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

			$this->statement->Execute($this->sql);
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

			/* quick fix since when updating or deleting, where's don't appear in the correct order */
			$id = md5(uniqid());
			$sql = $this->ProcessString($query_type, array(
				'column' => $filter[0],
				'operator' => $filter[1],
				'value' => '@where' . $id, 
				));

			if(is_int($filter[2])) {
				$this->statement->BindParameter('where' . $id,$filter[2], 'int');
			}

			else {
				$this->statement->BindParameter('where' . $id,$filter[2], 'string');
			}

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

		public function join($filter = array()) {
			$join_table = $filter[0];
			$table_col = $filter[1];
			$filter_type = $filter[2];
			$join_col = $filter[3];

			$select_sql = $this->ProcessString('joinselect', array(
				'table' => $this->table, 
				'filtertable' => $join_table,
			));

			$join_sql = $this->ProcessString('join', array(
				'table' => $join_table,
				'col1' => $table_col,
				'operator' => $filter_type,
				'col2' => $join_col,
			));

			$this->sql = $select_sql . ' ' . $join_sql;
			$this->metadata['join_table'] = $join_table;

			return $this;
		}

		public function limit($amount, $offset=0) {
			
			$this->limitstring = $this->ProcessString('limit', array(
			'amount' => '?',
			'offset' => '?',
			));

			$this->statement->AddParameter($amount, 'int');
			$this->statement->AddParameter($offset, 'int');

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



		//final methods
		public function results($type = 'object') {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			/* add on order statements and limits if exists */

			$this->sql .= ' ' . $this->orderstring;
			$this->sql .= ' ' . $this->limitstring;


			$this->statement->Execute($this->sql);


			if($type == 'object') {
				return $this->statement->FetchObject();
			}

			else if($type == 'array') {

				return $this->statement->FetchAll();
			}

			else if($type == 'one') {
				return $this->statement->FetchOne();
			}

			else if($type == 'json') {
			 	return json_encode($this->statement->FetchObject());
			}

			$this->statement = new DesmondDatabaseQuery($this->conn);

		}
		public function count() {

			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$this->statement->Execute($this->sql);

			$count = $this->statement->Count();


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
			
			$this->statement->Execute($this->sql);
			$this->statement = new DesmondDatabaseQuery($this->conn);

		}

		public function distinct() {
			
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$this->sql = str_replace('SELECT', 'SELECT DISTINCT', $this->sql);

			return $this;
		}


		public function groupby($col) {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$this->sql .=  ' ' . $this->ProcessString('groupby', array(
			'col' => $col,
			));
		}

		public function in($col, $data = array()) {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$values = array();

			foreach($data as $key => $value) {
				$values[$key] = '?';

				if(is_int($value)) {
					$this->statement->AddParameter($value, 'int');
				}

				else {
					$this->statement->AddParameter($value, 'string');
				}
			}

			$this->sql .=  ' ' . $this->ProcessString('in', array(
			'values' => implode(',', $values), 
			'col' => $col,
			));
		}

		public function insert($data = array()) {
			if($this->table == '') {
				throw new QueryBuilderNoTableException();
			}

			$cols = array_keys($data);

			$values = array_values($data);

			foreach($values as $key => $value) {
				$values[$key] = '?';

				if(is_int($value)) {
					$this->statement->AddParameter($value, 'int');
				}

				else {
					$this->statement->AddParameter($value, 'string');
				}
			}

			$sql = $this->ProcessString('insert', array(
			'table' => $this->table,
			'cols' => implode(',', $cols),
			'values' => implode(',', $values), 
			));
			
			$this->statement->Execute($sql);

			$insert_id = $this->statement->GetInsertID();
			$this->statement = new DesmondDatabaseQuery($this->conn);
			return $insert_id;

 		}


		public function update($data = array()) {
			$cols = array_keys($data);
			$values = array_values($data);

			$update_strings = array();

			foreach($cols as $key => $value) {
				$update_strings[$key] = $value . ' = ?';
				
				if(is_int($values[$key])) {
					$this->statement->AddParameter($values[$key], 'int');
				}

				else {
					$this->statement->AddParameter($values[$key], 'string');
				}
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

			$this->statement->Execute($update_sql . ' '. $sql);
			$this->statement = new DesmondDatabaseQuery($this->conn);
			
			return $this;
		}

		public function raw($sql) {

			if($this->sql == '') {
				$this->sql .= $sql;
			}

			else {
				$this->sql .= ' ' . $sql;
			}

			return $this;
		}
 
		public function sql() {
			return $this->sql;
		}

		private function ProcessString($string, $vars) {

			$string = $this->strings[$string];
			foreach($vars as $key => $value) {
				$string = str_replace('@' . $key, $value, $string);
			}

			return $string;
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

<?php
	interface IQueryBuilder {

		function __construct(IDatabaseConnection $conn);

		//builder methods
		public function table($name);
		public function where($filter = array());
		public function columns($cols = array());
		public function raw($sql);
		public function distinct();

		//final methods
		public function results($type = 'object');
		public function count();
		public function delete();
		public function truncate();
		public function insert($data = array());
		public function orderby($col, $order);
		public function join($filter = array());
		public function limit($amount, $offset=0);


		/* stub */

		/*public function cache($seconds);*/

		//misc
		public function sql();
	}
?>
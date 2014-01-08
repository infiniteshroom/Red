<?php 

interface IDatabaseConnection {
	public function Open($params = array());
	public function GetDBConnection();
	public function Close();
}

?>
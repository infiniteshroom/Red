<?php 

Application::Import('Desmond::Database::DBAL::Drivers::IDatabaseConnection.php');

interface IDatabaseQuery {
	public function __construct(IDatabaseConnection $conn);
	public function Execute($sql);
	public function GetInsertID();
	public function FetchOne();
	public function FetchAll();
	public function FetchObject();
	public function Count();
	public function Escape($input);
	public function AddParameter($param, $type);
}

?>

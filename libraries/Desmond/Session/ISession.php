<?php
interface ISession {
	public function Get($name);
	public function Set($name,$value);
	public function Create($args=null);
	public function Save();
	public function Destroy();
	public function RememberMe($value);
}
?>

<?php
interface IDesmondObject {

	/* wtf, yeah we'll come to this but it makes desmond very special =p */
	public static function override($object);
	public static function whoami();

	//public function toString();
	//public function toJSON();
	//public function toXML();
}

?>
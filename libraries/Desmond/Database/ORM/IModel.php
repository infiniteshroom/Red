<?php
	interface IModel {
			public function Create();
			public function Save();
			public function Delete();
			public static function All();
			public static function Find($value);
			public function GetBuilder();
			public function GetJson();	
			public function GetAttributeNames();
			public function GetTable();
	}

?>
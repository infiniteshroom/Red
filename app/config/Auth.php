<?php 
	
	return array(
		/* type of auth to use either "Database" or "Model" - This is the return type of the auth class */
		'type' => 'Model',

		/* if database - we need to know a few things. */
		'datasource' => 'default',
		'table' => 'Users',

		/* if model - we need to know a few things */
		'model' => 'Users',

		/* Attributes - needed for both ORM and Database */
		'attributes' => array(
			'username' => 'username',
			'password' => 'password',
		),

		/* hash type - if unsure leave as "Blowfish" */
		'hash' => 'Blowfish'


	);

?>
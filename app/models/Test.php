<?php
class Test extends Model {
	protected $table = 'test';
	protected $validators = array(
		'required' => array(
			array('string'),
			array('num'),
		),

		'integer' => 'num',
		'alpha' => 'string',
	);
}

?>

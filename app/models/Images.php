<?php
class Images extends Model {
	protected $relationships = array(
		'Comments' => array(
			'model' => 'ImageComments',
			'key' => 'image_id'
			),
	);

	protected $table = 'Images';
}

?>

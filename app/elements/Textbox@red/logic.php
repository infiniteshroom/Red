<?php

class TextboxElement extends DesmondElement {
	protected $variables = array(
		'id' => '',
		'value' => '',
	);

	
	public function setText($value) {
		$this->value = $value;
		
		$this->Set('value', $this->value);
	}
}

?>

<?php

class DesmondElement {
	protected $UUID;
	protected $name;
	protected $description;
	protected $version;
	protected $variables = array();

	public function Start() {
		 $rc = new ReflectionClass(get_class($this));
         $dir = $this->getWidgetPath();

        //let's get the metadata
		$metadata_raw = file_get_contents($this->getDir() . '/metadata.json');
	    $metadata_array = json_decode($metadata_raw, true);
	    
	    //now let's add the data to the class.
	    $this->name = $metadata_array['name'];
	    $this->UUID = $metadata_array['uuid'];
	    $this->version = $metadata_array['version'];
	    $this->description = $metadata_array['description'];

	}

	public function Render() {
		$element_path = $this->getWidgetPath() . '/';

		echo Template::Buffer('layout.html', $this->variables, $element_path);
	}

	public function Set($name, $value) {
		$this->variables[$name] = $value;
	}

	public function GetUUID() {
		return $this->UUID;
	}
	
	public function GetName() {
		return $this->name;
	}
	
	public function GetDescription() {
		return $this->description;
	}
	
	public function GetVersion() {
		return $this->version;
	}

	public function NewInstance() {
		$class = get_class($this);
		return $class;
	}
	

	private function getWidgetPath() {
		$rc = new ReflectionClass(get_class($this));
        return dirname($rc->getFileName());
	}
}

?>
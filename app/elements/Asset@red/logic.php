<?php

class AssetElement extends DesmondElement {

	public function getAssetPath() {
		/* remove index.php */

		return Application::Path('web');
	}
	public function js($file) {

		$root = $this->getAssetPath();

		echo "<script src='$root/assets/$file' type='text/javascript'></script>";
	}

	public function css($file) {

		$root = $this->getAssetPath();

		echo "<link href='$root/assets/$file' rel='stylesheet'>";
	}
}

?>

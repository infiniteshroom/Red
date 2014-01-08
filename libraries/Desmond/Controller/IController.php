<?php
interface IController {

	/* setup the controller */
	public function Init();

	/* allows you to set variables for the controller */
	public function Set($data = array());

	/* returns content to the screen */
	public function Render();

	/* sets if the controller is restful or not, controllers are restful by default */
	public function Restful($value);

	/* override default template */
	public function SetView($name);


}
?>
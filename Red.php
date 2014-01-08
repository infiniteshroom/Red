<?php
	/* red cli - this functions as a admin interface for the framework */

	$app_path = include_once('paths.php');


	include_once($app_path['libraries'] . 'Desmond/Bootstrap/IDesmondObject.php');
	include_once($app_path['libraries'] . 'Desmond/Bootstrap/DesmondObject.php');
	include_once($app_path['libraries'] . 'Desmond/Bootstrap/Desmonds.php');
	include_once($app_path['libraries'] . 'Desmond/Bootstrap/Application.php');
	include_once($app_path['libraries'] . 'Desmond/Bootstrap/ModulesLoader.php');


	/* assign core desmond objects for application and moduleloader */
	ModulesLoader::override(new DesmondModulesLoader());
	Application::override(new DesmondApplication());

	Application::SetMode('cli');
	Application::Start();

?>
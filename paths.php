<?php
	return array(
		'app' => __DIR__ . '/app/',
		'assets' => __DIR__ . '/assets/',
		'libraries' => __DIR__ . '/libraries/',
		'temp' => __DIR__ . '/temp/',
		'web' => str_replace('index.php', '', dirname($_SERVER['REQUEST_URI'])),
	);
?>

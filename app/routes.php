<?php


function isAdmin() {
	return true;
}

	//Router::Route('home')->where(isAdmin())->controller('HomeController')->method('GET')->add();

	Router::Map('/', 'HomeController');
	Router::Map('home', 'HomeController');

	Router::Map('/db', function() {
	 Kint::dump(Users::where(array('id', '=', 1))->results());
	});
?>

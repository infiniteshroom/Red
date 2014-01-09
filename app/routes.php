<?php


/*function isAdmin() {
	return true;
}

Router::Route('/product/view')->host('')->parameter('id', int)
->where(!isAdmin())->controller('ProductViewController')->method('GET')->add();*/

	Router::Map('/', 'HomeController');
	Router::Map('home', 'HomeController');

	Router::Map('/db', function() {
		var_dump(Images::find(1216)->Relation('Comments')->results('json'));
	})
?>

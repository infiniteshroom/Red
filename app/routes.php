<?php


/*function isAdmin() {
	return true;
}

Router::Route('/product/view')->host('')->parameter('id', int)
->where(!isAdmin())->controller('ProductViewController')->method('GET')->add();*/

	Router::Map('/', 'HomeController');
	Router::Map('home', 'HomeController');

	Router::Map('/db', function() {
		Kint::dump(Images::find(2169)->Relation('Comments')->results());
	});
?>

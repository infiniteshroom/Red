<?php
	interface IAuth {

		/* login user using username and password */
		public function Login($username, $password);

		/* check if current user is guest */
		public function isGuest();

		/* check if details are valid, but don't login */
		public function Check($username, $password);

		/* logout */
		public function Logout();

		/* get details */
		public function User();

		/* spoof user */
		public function Spoof($id);
	}
?>
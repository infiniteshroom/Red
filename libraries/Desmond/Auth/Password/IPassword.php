<?php
	interface IPassword {
		/* create a hash based a provided plain text version */
		public function Create($password);

		/* check if password provided matches hash */
		public function Check($hash, $plain);
	}
?>
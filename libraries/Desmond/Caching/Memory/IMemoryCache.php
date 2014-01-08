<?php
interface IMemoryCache {
	
	/* let's add variables to the session and pull them back */
	function CreateMemoryHandler($name);
	function WriteCacheData($handler, $data, $time);
	function ReadCacheData($handler);
	function IsExpired($handler);
	function IsCreated($handler);
	
}
?>

<?php
	define('SERVER', 'localhost');
	define('USERNAME', 'root');
	define('PASSWORD', 'meinvt');
	define('DB', 'uploader');
	$connection = mysqli_connect(SERVER, USERNAME, PASSWORD, DB);
	if(!$connection) {
		die('server error');
	}
?>
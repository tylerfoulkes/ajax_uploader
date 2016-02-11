<?php 
require_once('connection.php');
require_once('upload.php');

define('DIR', '/');
define('TABLE', 'homepage');
define('COLUMN', 'banner_image');
define('COLUMN_TWO', 2);
define('TYPE', 'INSERT INTO');
define('NUM', 10);

if (isset($_POST['image_save'])) {
	upload::get_connection($connection);
	upload::database_query(DIR, TYPE, TABLE, COLUMN, COLUMN_TWO, 1);
	foreach (upload::$messages as $value) {
		echo  $value . "</p>";
	}
}

if (isset($_POST['img'])) {
	foreach($_POST['img'] as $key) {
		upload::delete_temp($connection, $key);
	}
	refresh_previews($connection);
}	

if (isset($_FILES['image'])) {
	$new_upload = new upload($connection, $_FILES['image']['tmp_name'], NUM);
	// foreach (upload::$messages as $value) {
	// 	echo  $value . "</p>";
	// }
	refresh_previews($connection);
}

function refresh_previews($connection) {
	$query = "SELECT * FROM temp_images";
	$result = mysqli_query($connection, $query);
	while ($row = mysqli_fetch_assoc($result)) {
		if (is_file(upload::$temp . $row['image_link'])) {
			echo "<li><img class='temp_img' src='" 
			. upload::$temp . $row['image_link'] . "'/>";
			echo "
			<input class='image_button' type='checkbox' name='img[]' value='" . $row['image_link'] . "'>";
		}
	} 
}
?>
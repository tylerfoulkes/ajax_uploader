<?php

################
# UPLOAD CLASS #
################

class upload
{
	//creates global connection variable for the class
	protected static $connection;

	//creates a variable that holds styling for error messages
	public static $style = "<p class='error'>";

	//creates an array that will hold messages for different errors
	public static $messages = array();

	//variable that holds the maximum number images allowed to upload
	public $num_files = null;

	//creates a variable that will hold the temporary image
	public $file = null;

	//creates the temporary desination directory for the image
	public static $temp = "../images/temp/";

	//creates the desination directory for the image
	public static $destination = "../images/";

	//constructor for the class
	public function __construct($in_connection, $in_file, $in_num) {
		$this->file = $in_file;
		$this->num_files = $in_num;
		upload::get_connection($in_connection);
		if (is_dir(upload::$temp) && is_dir(upload::$destination) 
		&& is_writable(upload::$temp) && is_writable(upload::$destination)) {
			if (!isset($_SESSION['page'])) {
				$this->check_file();
			}
			else if (isset($_SESSION['page']) && (count($_SESSION['page']) < $this->num_files)) {
				$this->check_file();
			}
			else {
				upload::$messages[] = upload::$style . "You have uploaded the max number of
												images alloted";
			}
		}
		else {
			upload::$messages[] = upload::$style . "File upload feature is not available";
		}
	}

	public static function get_connection($in_connection) {
		upload::$connection = $in_connection;
	}

	//checks to see if their was a file chosen
	public function check_file() {
		$error = $_FILES['image']['error'];
		switch ($error) {
			case UPLOAD_ERR_NO_FILE:
				upload::$messages[] = upload::$style . "Please select a file to upload";
				break;

			case UPLOAD_ERR_PARTIAL:
				upload::$messages[] = upload::$style . "File was unable to upload, 
													please try again";
				break;

			case UPLOAD_ERR_INI_SIZE:
				upload::$messages[] = upload::$style . "File is too big, 
													please select a file under
													2 Megabytes";
				break;

			case UPLOAD_ERR_CANT_WRITE:
				upload::$messages[] = upload::$style . "File was unable to upload, 
													please try again";
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				upload::$messages[] = upload::$style . "File was unable to upload, 
													please try again";
				break;

			case UPLOAD_ERR_OK:
				$this->check_file_type();
				break;
		}
	}

	//checks to see if the file uploaded is of the right type
	public function check_file_type() {
		//creates a boolean variable
		$yes = false;
		//creates an array of allowed mime types
		$types = array("jpg"=>'image/jpeg',
						"png"=>'image/png',
						"tiff"=>'image/tiff');
		//checks the mime type of the file and saves it into a variable
		// $mime_type = mime_content_type($this->file);
    	
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $this->file);
       
    	
		//runs a for each loop to show contents of types array
		foreach ($types as $value) {
			/*if the mime type of the file matches a mime type in the array
			  set boolean to true*/
			if ($mime_type == $value) {
				$yes = true;
			}
		}
		finfo_close($finfo);
		/*if mime type isn't matched in the foreach loop put 
		error in messages array*/
		if (!$yes) {
			upload::$messages[] = upload::$style . "Please select a different filetype,
												permitted file types 
												(jpeg, png, tiff)";
		}
		//if the mime type matches allowed mime type call trim file function
		else {
			$this->trim_filename();
		}
	}

	//replaces the filename with an incremented number
	public function trim_filename() {
		//querys the count table to get the current
		$query = "SELECT * FROM image_count ";
		$result = mysqli_query(upload::$connection, $query);
		if (!$result) {
			die("Database query failed. ");
		}
		$row = mysqli_fetch_assoc($result);
		//adds one to the count
		$count = 1 + $row["name"];
		//querys the database to enter the new count into the count table
		$query = "UPDATE image_count SET name = '{$count}' WHERE count = 1";
		$result = mysqli_query(upload::$connection, $query);
		if (!$result) {
			die("Database query failed. ");
		}

		/*
		call input function to sanitize file name
		*/

		//gets file name of the uploaded file
		$file = $_FILES['image']['name'];
		//seperates the file string to get the file extension
		$file = explode(".", $file);
		$file = end($file);
		//concatinates the count to the file extension
		$file = $count . "." . $file;
		//saves the new name into the global files array
		$_FILES['image']['name'] = $file;
		//calls the final check function
		$this->final_check();
	}

	//performs the final check and moves the file/queries the database
	public function final_check() {
		$verdict = null;
		foreach (upload::$messages as $value) {
			if (!is_null($value)) {
				$verdict = 1;
			}
		}
		if (is_null($verdict)) {
			move_uploaded_file($this->file, upload::$temp . $_FILES['image']['name']);
    		// initialize array only if it not already exists:
			if(!isset($_SESSION['page']) && !isset($_SESSION['comment'])) {
    			$_SESSION['page'] = array();
    			$_SESSION['comment'] = array();
			}
			$_SESSION['page'][] = $_FILES['image']['name'];
			//sets a success message
			upload::$messages[] = "<p class='verdict'>"
								. "File uploaded successfully";
		}
		else {
			upload::$messages[] = upload::$style . "Something went wrong, please try
												again later.";
		}
	}

	//queries the database and stores the image path
	public static function database_query($dir, $type, $table, $column,
	$column_two, $id) {
    	if (is_dir(upload::$temp) 
    	&& is_dir(upload::$destination . $dir) 
    	&& is_writable(upload::$destination . $dir)) {
    		if (!empty($_SESSION['page'])) {	
    			foreach ($_SESSION['page'] as $value) {
    				$rename = rename(upload::$temp . $value, upload::$destination . $dir . $value);
    				unset ($_SESSION['page']);
    				if (is_null($column_two)) {
    					$query = "$type $table SET $column = '{$value}'";

    				}
    				else {
    					$query = "$type $table ($column,
    						$column_two) VALUES ('{$value}', $id)";
    				}
					$result = mysqli_query(upload::$connection, $query);
					if (!$result) {
		 				die("Database query failed. ");
					}
				}
				upload::$messages[] = "<p class='verdict'>" . "Images have been successfully saved";
    		}
    		else {
    			upload::$messages[] = upload::$style . "Please upload an image";
    		}
    	}
    	else {
    		upload::$messages[] = upload::$style . "Images could not be saved, please try again";
    	}
	}

	public static function delete_temp($image) {
		if (is_dir(upload::$temp) && is_writable(upload::$temp)) {
			$path = upload::$temp . $image;
			if (is_file($path)) {
				unlink ($path);
				$key = array_search($image, $_SESSION['page']);
    			unset($_SESSION['page'][$key]);
				$_SESSION["page"] = array_values($_SESSION["page"]);
				upload::$messages[] = upload::$style . "Image was deleted";
			}
		}
		else {
			upload::$messages[] = upload::$style . "Image could not be deleted, please try again";
		}
	}

	public static function delete_image($table, $column, $image, $dir) {
		if (is_dir(upload::$temp) 
		&& is_writable(upload::$temp)
    	&& is_dir(upload::$destination) 
    	&& is_writable(upload::$destination)) {
    		$path = upload::$destination . $dir . $image;
    		if (is_file($path)) {
    			unlink ($path);
				$query = "DELETE FROM $table WHERE $column = '{$image}'";
				$result = mysqli_query(upload::$connection, $query);
				if (!$result) {
		 			die("Database query failed. ");
				}
				upload::$messages[] = "<p class='verdict'>" . "Image was deleted successfully";
			}
			else {
				upload::$messages[] = upload::$style . "Image can not be deleted, please try again";
			}
		}
		else {
			upload::$messages[] = upload::$style . "Image can not be deleted, please try again";
		}
	}
}
?>
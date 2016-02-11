<html>
	<head>
		<?php require_once('header.php'); ?>
			<div id="upload_form">
				<form action="process.php" method="POST" id="homepage" name="homepage_banner" 
				enctype="multipart/form-data">
					<h1>Select and image to upload</h1>
					<input type="file" name="image" multiple="multiple" id="choose">
					<input type="submit" name="banner_upload" value="Upload" id="upload">
				</form>
				<div id="output"></div>
				<div id="uploaddiv" ><div id="uploadbar"></div><div id="percentcomplete">0%</div></div>
			</div>
			<?php require_once('process.php'); ?>
			<form method='POST' action='process.php' id='delete_form'>
				<h1>Uploaded images</h1>
				<ul class='previews'></ul>
				<input type='submit' name='img_submit' value='Delete selected images' id="delete"></li>
			</form>
			<hr>
		</div>
	</body>
</html>
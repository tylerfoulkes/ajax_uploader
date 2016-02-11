$(document).ready(function() {
	$('#delete').show();
	$('#upload').show();
	$('#save').show();

	var button = document.getElementById('delete');
	button.addEventListener('click', function(e) {
		var checkbox = $('.image_button').is(':checked');
		if(checkbox == 1) {
			var optionstwo = { 
				target:   '.previews',   // target element(s) to be updated with server response 
				beforeSubmit: beforeDelete,
				success: afterDelete,
				resetForm: true        // reset the form after successful submit 
			}; 
				
			$('#delete_form').submit(function() { 
				$('#delete_form').ajaxSubmit(optionstwo);  			
				// always return false to prevent standard browser submit and page navigation 
				return false; 
			});
		}
		else {
			e.preventDefault();
			$('#output').html('Please select and image to delete');
		} 
	});

	var options = { 
		target:   '.previews',   // target element(s) to be updated with server response 
		beforeSubmit:  beforeSubmit,  // pre-submit callback 
		success:       afterSuccess,  // post-submit callback 
		uploadProgress: OnProgress, //upload progress callback 
		resetForm: true        // reset the form after successful submit 
	}; 
		
	$('#homepage').submit(function() { 
		$(this).ajaxSubmit(options);  			
		// always return false to prevent standard browser submit and page navigation 
		return false; 
	}); 

	//function before successful delete
	function beforeDelete() {
		$('#output').empty();
		$('#delete').hide();
		$('#output').html('Images are being deleted...');
	}

	//function after successful delete
	function afterDelete() {
		$('#output').empty();
		$('#output').html('Images were deleted.');
		$('#delete').show();
	}

	//function after succesful file upload (when server response)
	function afterSuccess() {
		$('#output').empty();
		$("#output").html("Image uploaded successfully!"); 
		$('#upload').show(); //hide submit button
		$('#uploaddiv').hide(); //hide progress bar
	}

	//function to check file size before uploading.
	function beforeSubmit() {
	    //check whether browser fully supports all File API
	   if (window.File && window.FileReader && window.FileList && window.Blob) {
			$('#output').empty();

			if( !$('#choose').val()) {
				$("#output").html("Please select a file to upload.");
				return false
			}
			
			var fsize = $('#choose')[0].files[0].size; //get file size
			var ftype = $('#choose')[0].files[0].type; // get file type
			
			//allow file types 
			switch(ftype) {
	            case 'image/png': 
				// case 'image/gif': 
				case 'image/jpeg': 
				case 'image/pjpeg':
				// case 'text/plain':
				// case 'text/html':
				// case 'application/x-zip-compressed':
				// case 'application/pdf':
				// case 'application/msword':
				// case 'application/vnd.ms-excel':
				// case 'video/mp4':
	                break;
	            default:
	                $("#output").html("<b>"+ftype+"</b> is an unsupported files type,"
	                	+ " Please upload a jpeg or png image file.");
					return false;
	        }
			
			//Allowed file size is less than 5 MB (1048576)
			//5242880	
			if(fsize > 2000000) {
				$("#output").html("The file you selected is, " + fileSize(fsize) +
					"<br />your file must be less than 2 MB.");
					return false;
			}	
			$('#upload').hide(); //hide submit button 
		}
		else {
			//Output error to older unsupported browsers that doesn't support HTML5 File API
			$("#output").html("Please upgrade your browser," +
				" because your current browser lacks some new features we need!");
			return false;
		}
	}

	//progress bar function
	function OnProgress(event, position, total, percentComplete) {
	    //progress bar
		$('#uploaddiv').show();
	    $('#uploadbar').width(percentComplete + '%') //update progressbar percent complete
	    $('#percentcomplete').html(percentComplete + '%'); //update status text
	    if(percentComplete>50)
	        {
	            $('#percentcomplete').css('color','#000'); //change status text to white after 50%
	        }
	}

	//function to format bites bit.ly/19yoIPO
	function fileSize(bytes) {
	   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	   if (bytes == 0) return '0 Bytes';
	   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
	}
}); 
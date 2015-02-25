$(document).ready(function (e) {
	$("#uploadimage").on('submit',(function(e) {
		e.preventDefault();
		$("#message").empty();
		$('#loading').show();
		$.ajax({
				url: "photo_methods.php", // Url to which the request is send
				type: "POST",             // Type of request to be send, called as method
				data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
					$('#loading').hide();

					if(data.indexOf('Sorry') >= 0){
						$("#message").html(data);	
					}else if(data.indexOf('images/uploaded') >= 0){
						$("#message").html('<img id="new_image_with_logo" src="'+data+'" />');
						$('#new_image_with_logo').attr('width', '250px');
						$('#new_image_with_logo').attr('height', '230px');
						SaveToDisk(data);
					}
					
				}
		});
}));

	// Function to preview image after validation
	$(function() {
		$("#fileToUpload").change(function() {
			$("#message").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#previewing').attr('src','noimage.png');
				$("#message").html("<p id='error'>Please Select A valid Image File</p>"+"<h4>Note</h4>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});
	function imageIsLoaded(e) {
		$("#fileToUpload").css("color","green");
		$('#image_preview').css("display", "block");
		$('#previewing').attr('src', e.target.result);
		$('#previewing').attr('width', '250px');
		$('#previewing').attr('height', '230px');
	};

	function SaveToDisk(fileURL, fileName) {
	    // for non-IE
	    fileName = fileName || 'new_image';
	    if (!window.ActiveXObject) {
	        var save = document.createElement('a');
	        save.href = fileURL;
	        save.target = '_blank';
	        save.download = fileName ;

	        var event = document.createEvent('Event');
	        event.initEvent('click', true, true);
	        save.dispatchEvent(event);
	        (window.URL || window.webkitURL).revokeObjectURL(save.href);
	    }

	    // for IE
	    else if ( !! window.ActiveXObject && document.execCommand)     {
	        var _window = window.open(fileURL, '_blank');
	        _window.document.close();
	        _window.document.execCommand('SaveAs', true, fileName || fileURL)
	        _window.close();
	    }
	}
});

<!DOCTYPE html>
<html>
<head>
	<title>Ajax Image Upload Using PHP and jQuery</title>
	<link rel="stylesheet" href="assets/style.css" />
	<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
	<div class="main">
	<h1>Merging Image Machine</h1><br/>
	<hr>
	<form id="uploadimage" action="" method="post" enctype="multipart/form-data">
	<div id="image_preview"><img id="previewing" src="images/noimage.png" /></div>
	<hr id="line">
	<div id="selectImage">
	<label>Select Your Image</label><br/>
	<input type="file" name="fileToUpload" id="fileToUpload" required />
	<input type="submit" value="Upload" class="submit" />
	</div>
	</form>
	</div>
	<h4 id='loading' >loading..</h4>
	<div id="message"></div>
</body>
<script src="assets/script.js"></script>
</html>

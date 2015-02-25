<?php 

$target_dir = 'images/';
$photo_watermark_name = $target_dir.'wm.png';
$target_dir = "{$target_dir}uploaded/";
$photo_final_name = $target_dir.'final_image'.mt_rand().'.jpg';
$photo_temfile = $target_dir.'temporal_image'.mt_rand().'.jpg';
if ($photo_background_name = upload_file($target_dir)){
	list($max_width, $max_height) = getimagesize($photo_background_name);
	list($logo_width, $logo_height) = getimagesize($photo_watermark_name);

	if(validate_dimensions($max_width, $max_height))
	{
		$photo_background_name = create_new_photo_resized($photo_background_name, $photo_temfile);
		list($max_width, $max_height) = getimagesize($photo_background_name);
	}

	merge_images($photo_background_name, $photo_watermark_name, $max_width, $max_height, $photo_final_name, $logo_width, $logo_height);
	if (file_exists( $photo_background_name )) unlink($photo_background_name);
	if (file_exists( $photo_temfile )) unlink($photo_temfile);
	deleteDir('cache');

echo $photo_final_name;
}

function upload_file($target_dir){
	$tempFile = $_FILES["fileToUpload"];
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

	if(!is_file_a_image($tempFile)){
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		var_dump($tempFile);
	}else if ($_FILES["fileToUpload"]["size"] > 800000) {
	    echo "Sorry, your file is too large.";
	}else if(!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
		echo "Sorry, there was an error uploading your file.";
	}else{
		return $target_file;
	}
}

function is_file_a_image($tempFile){
	$check = getimagesize($tempFile["tmp_name"]);
	$allowed_extensions = array('gif','jpg','png','jpeg');
	$imageFileType = pathinfo($tempFile['name'],PATHINFO_EXTENSION);
    return ($check !== false) && in_array($imageFileType, $allowed_extensions);
}

function set_principal_image_config($width, $height){
	$dest_image = imagecreatetruecolor($width, $height);
		//make sure the transparency information is saved
	imagesavealpha($dest_image, true);
		//create a fully transparent background (127 means fully transparent)
	$trans_background = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);
		//fill the image with a transparent background
	imagefill($dest_image, 0, 0, $trans_background);
	return $dest_image;
}

function merge_images($photo_background_name, $photo_watermark_name, $max_width, $max_height, $photo_final_name, $logo_width, $logo_height){
	$dest_image = set_principal_image_config($max_width, $max_height);

	$photo_background = imagecreatefromjpeg($photo_background_name);
	$photo_watermark = imagecreatefrompng($photo_watermark_name);

	//copy each png file on top of the destination (result) png
	$logo_x_position = ($max_width-$logo_width)/2;
	$logo_y_position = $max_height-$logo_height;
	imagecopy($dest_image, $photo_background, 0, 0, 0, 0, $max_width, $max_height);
	imagecopy($dest_image, $photo_watermark, $logo_x_position, $logo_y_position, 0, 0, $logo_width, $logo_height);

	//send the appropriate headers and output the image in the browser
	//header('Content-Type: image/png');
	//imagepng($dest_image);
	imagepng($dest_image,$photo_final_name);

	//destroy all the image resources to free up memory
	imagedestroy($photo_background);
	imagedestroy($photo_watermark);
	imagedestroy($dest_image);
}

function create_new_photo_resized($photo_path, $new_photo_name){
	$serv = "http://".$_SERVER["SERVER_NAME"];
	list($width, $height) = getimagesize($photo_path);
	list($width, $height) = set_new_dimensions($width, $height);
	$contents = file_get_contents($serv.'/imageloader/timthumb.php?src='.$photo_path.'&w='.$width.'&h='.$height.'&zc=0');

	$file = fopen($new_photo_name,"w");
	fwrite($file,$contents);
	fclose($file);
	return $new_photo_name;
}

function set_new_dimensions($width, $height){
	for(;$percentage_scale = validate_dimensions($width, $height);){
		$width *= $percentage_scale;
		$height *= $percentage_scale;
	}
	return [$width, $height];
}

function validate_dimensions($width, $height){
	if($width > 800 && $height > 800){
		return 0.95;
	}
	if($width < 200 || $height < 200){
		return 1.05;
	}
}

function deleteDir($path) {
    return is_file($path) ?
            @unlink($path) :
            array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
}
?>

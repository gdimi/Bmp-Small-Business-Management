<?php
if (!defined('_w00t_frm')) die('har har har');

$scerr = '';
$msg = '';
$pos = $_POST['pos'];
$caseId = $_POST['cid'];


if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    require_once('sources/config.php');
    $dss = new DSconfig;
    
	$target_dir = "content/uploads/${thisYear}/${caseId}";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$fileSize = $_FILES["fileToUpload"]["size"];
	$fileTmpName = $_FILES["fileToUpload"]["tmp_name"];

	$scerr = process_upload($target_dir,$fileType,$fileTmpName,$fileSize,$dss->maxUploadSize,$dss->uploadTypes);

	// Check if we have an error and if not try to upload the file!
	if (!$scerr) {
		if (move_uploaded_file($fileTmpName, $target_file)) {
			$msg = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			$tk_status = json_encode(array(
			 'status' => 'success',
			 'message'=> $msg
			));
			echo $tk_status;
			exit(0);
		} else {
			$scerr = "Sorry, there was an error uploading your file.";
		}
	}
}

if ($scerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}

function process_upload($target_dir,$fileType,$fileTmpName,$fileSize,$maxUploadSize,$uploadTypes=array()) {
    
    clearstatcache(); //to avoid file_exists false reports

	if (!file_exists($target_dir)) {
		if (!mkdir($target_dir, 0777, true)) {
			return 'could not create directory:'.$target_dir;
		}
	}

	// Check if image file is a actual image or fake image
	if($fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'bmp' || $fileType == 'png' || $fileType == 'tiff' || $fileType == 'gif') {
		$check = getimagesize($fileTmpName);
		if($check === false) {
			return "File is propably a fake image.";
		}
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		return "Sorry, file already exists.($target_file)";
	}
	// Check file size
	if ($fileSize > ($maxUploadSize * 1024)) {
		return "Sorry, your file is too large. Maximum size allowed is:".$maxUploadSize." kbytes";
	}
	// Allow certain file formats
	if(!in_array($fileType,$uploadTypes)) {
		return  "Sorry, this file type ($fileType) is not allowed.";
	}
}

?>

<?php
	require_once("../includes/globals.php");
	require_once("../includes/functions.php");

	$imageDir = 'img/';
	$imageDirFullPath = APP_PATH . $imageDir;

	// make sure we start at the beginning of the FILES array
	reset($_FILES);

	// store value of current file in temp var
	$temp = current($_FILES);

	if (is_uploaded_file($temp['tmp_name'])) {

		// Sanitize input
    	if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
        	exit("Invalid file name.");
    	}

    	// Verify extension
	    if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
	        exit("Invalid file extension.");
	    }

	    // Make sure filename is unique
	    $fileName = make_unique_filename($temp['name'], $imageDir);

	    // Move temp file to image directory
	    $fileToWrite = $imageDirFullPath . $fileName;
    	move_uploaded_file($temp['tmp_name'], $fileToWrite);

    	// Respond to the successful upload with JSON.
    	$fileURL = APP_PATH_IP_ADDRESS . 'img/' . $temp['name'];
	    echo json_encode(array('fileURL' => $fileURL));
	}
?>

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// If fileid is missing, exit with error
	if (!isset($_GET['fileid'])) {
		exit('Error: Missing file id');
	}

	$sel_file = "
		SELECT filename
		FROM ". TABLE_FILE_UPLOAD ."
		WHERE file_upload_id = ?
	";
	$stmt = $conn->prepare($sel_file);
	$stmt->bind_param("i", $_GET['fileid']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($fileName);
	$stmt->fetch();

	// If file does NOT exist in DB, exit with error
	if ($stmt->num_rows == 0) {
		exit('Error: File does not exist');
	}

	// build path to file
	$filePath = APP_PATH . 'uploads/' . $fileName;

	// Open file
	$file = @fopen($filePath,"rb");
	$fileSize = fstat($file)['size'];

	// Force download
	header("Content-Length: " . $fileSize);
	header("Content-Disposition: attachment; filename=\"$fileName\"");

	// IE6-7 compatibility
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	// output the file in chunks to prevent potential memory problems when dealing with large files
	set_time_limit(0); // override default time limit - no time limit imposed	
	while(!feof($file))
	{
		print(@fread($file, 1024*8));
		ob_flush();
		flush();
	}
	fclose($file);
?>

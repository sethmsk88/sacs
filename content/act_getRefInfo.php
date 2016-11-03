<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure required var exists
	if (!isset($_POST['linkID'])) {
		exit;
	}

	$sel_link = "
		SELECT linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_link);
	$stmt->bind_param("i", $_POST['linkID']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkName, $linkURL, $refNum);
	$stmt->fetch();

	// Check to see if reference has a file associated with it
	$sel_fileAssoc = "
		SELECT a.appendix_link_id, a.file_upload_id, f.fileName
		FROM ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." a
		JOIN ". TABLE_FILE_UPLOAD ." f
		ON a.file_upload_id = f.file_upload_id
		WHERE a.appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_fileAssoc);
	$stmt->bind_param("i", $_POST['linkID']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkID, $fileID, $fileName);
	$stmt->fetch();

	$isFileRef = false;
	if ($stmt->num_rows > 0) {
		$isFileRef = true;
		$json_arr['fileName'] = $fileName;
		$json_arr['linkID'] = $linkID;
		$json_arr['fileID'] = $fileID;
	}

	$json_arr['linkName'] = $linkName;
	$json_arr['linkURL'] = $linkURL;
	$json_arr['refNum'] = $refNum;
	$json_arr['isFileRef'] = $isFileRef;
	

	echo json_encode($json_arr);
?>

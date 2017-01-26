<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure required var exists
	if (!isset($_POST['linkID'])) {
		exit;
	}

	$sel_link = "
		SELECT l.appendix_link_id, l.linkName, l.linkURL, l.refNum, f.file_upload_id, f.fileName
		FROM " . TABLE_APPENDIX_LINK . " AS l
		LEFT JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON lhf.appendix_link_id = l.appendix_link_id
		LEFT JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON lhf.file_upload_id = f.file_upload_id
		WHERE l.appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_link);
	$stmt->bind_param("i", $_POST['linkID']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkID, $linkName, $linkURL, $refNum, $fileID, $fileName);
	$stmt->fetch();

	if (!is_null($fileID)) {
		$json_arr['fileName'] = $fileName;
		$json_arr['linkID'] = $linkID;
		$json_arr['fileID'] = $fileID;
	}

	$json_arr['linkName'] = $linkName;
	$json_arr['linkURL'] = $linkURL;
	$json_arr['refNum'] = $refNum;

	echo json_encode($json_arr);
?>

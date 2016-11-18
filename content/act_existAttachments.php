<?php
	require_once "../includes/globals.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	$numAttachedFiles = 0;

	// Check to see if there are any attachments for this SRID
	if (isset($_POST['srid'])) {
		$sel_attachments = "
			SELECT f.file_upload_id
			FROM ". TABLE_APPENDIX_LINK ." AS l
			JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON lhf.appendix_link_id = l.appendix_link_id
			JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON f.file_upload_id = lhf.file_upload_id
			WHERE l.srid = ?
		";
		$stmt = $conn->prepare($sel_attachments);
		$stmt->bind_param('i', $_POST['srid']);
		$stmt->execute();
		$stmt->store_result();
		$numAttachedFiles =  $stmt->num_rows;
	}
	
	echo $numAttachedFiles; // Return number of attached files	
?>

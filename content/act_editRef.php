<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure lid was posted
	if (!isset($_POST['link_id'])) {
		exit; // TODO: Create Error Msg - required variable missing
	}

	// update reference in DB
	$update_ref = "
		UPDATE " . TABLE_APPENDIX_LINK . "
		SET linkName = ?,
			linkURL = ?
		WHERE appendix_link_id = ?
	";
	if (!$stmt = $conn->prepare($update_ref)) {
		echo 'error: (' . $conn->errno . ') ' . $conn->error;
	} else if (!$stmt->bind_param("ssi",
		$_POST['refName'],
		$_POST['refURL'],
		$_POST['link_id'])) {
		echo 'error: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else if (!$stmt->execute()) {
		echo 'error: (' . $stmt->errno . ') ' . $stmt->error;
	} else {
		header("Location: " . APP_PATH_URL . "?page=appendix&id=" . $_POST['srid']);
	}
?>

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure SRID was posted
	if (!isset($_POST['SRID'])) {
		exit; // TODO: Create Error Msg - required variable missing
	}

	// update SR in DB
	$update_sr = "
		UPDATE " . TABLE_STANDARD_REQUIREMENT . "
		SET number = ?,
			descr = ?,
			narrative = ?,
			summary = ?,
			sr_type = ?,
			compliance = ?
		WHERE id = ?
	";
	if (!$stmt = $conn->prepare($update_sr)) {
		echo 'error: (' . $conn->errno . ') ' . $conn->error;
	}
	else if (!$stmt->bind_param("sssssii",
		$_POST['srNum'],
		$_POST['descr'],
		$_POST['narrative'],
		$_POST['summary'],
		$_POST['SRType'],
		$_POST['compliance'],
		$_POST['SRID'])) {
		echo 'error: (' . $stmt->errno . ') ' . $stmt->error;
	}
	else if (!$stmt->execute()) {
		echo 'error: (' . $stmt->errno . ') ' . $stmt->error;
	}

?>
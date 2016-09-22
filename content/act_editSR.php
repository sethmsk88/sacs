<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure SRID was posted
	if (!isset($_POST['SRID'])) {
		exit; // TODO: Create Error Msg - required variable missing
	}

	// update SR in DB
	$update_sr = "
		UPDATE sacs.standard_requirement
		SET number = ?,
			intro = ?,
			narrative = ?,
			summary = ?,
			sr_type = ?
		WHERE id = ?
	";
	$stmt = $conn->prepare($update_sr);
	$stmt->bind_param("sssssi",
		$_POST['srNum'],
		$_POST['intro'],
		$_POST['narrative'],
		$_POST['summary'],
		$_POST['SRType'],
		$_POST['SRID']);
	$stmt->execute();

?>
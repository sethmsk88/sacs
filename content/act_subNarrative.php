<?php
	define("APP_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/sacs/");

	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Insert new section into section table
	$ins_section = "
		INSERT INTO sacs.section (srid, name)
		VALUES (?,?)
	";
	$stmt = $conn->prepare($ins_section);
	$stmt->bind_param("is", $_POST['srid'], $_POST['sectionName']);
	$stmt->execute();

	header('Location: ' . APP_PATH . '?page=editSubNarrative&id=' . $_POST['srid']);
?>

<?php
	define("APP_PATH_URL", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/sacs/");

	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Determine what type of action should be performed
	if (isset($_POST['srid'])) {

		// Set parent section
		if (isset($_POST['parentSection'])) {
			$parentID = $_POST['parentSection'];
		} else {
			$parentID = -1; // No parent
		}

		// Insert new section into section table
		$ins_section = "
			INSERT INTO sacs.section (srid, name, parent_id)
			VALUES (?,?,?)
		";
		$stmt = $conn->prepare($ins_section);
		$stmt->bind_param("isi", $_POST['srid'], $_POST['sectionName'], $parentID);
		$stmt->execute();

		header('Location: ' . APP_PATH_URL . '?page=editSubNarrative&id=' . $_POST['srid']);

	} else if (isset($_POST['sid'])) {

		// Update section body in section table
		$update_section = "
			UPDATE sacs.section
			SET	body = ?
			WHERE id = ?
		";
		$stmt = $conn->prepare($update_section);
		$stmt->bind_param("si", $_POST['sectionBody'], $_POST['sid']);
		$stmt->execute();

		// Get srid for redirect
		$sel_srid = "
			SELECT srid
			FROM sacs.section
			WHERE id = ?
		";
		$stmt = $conn->prepare($sel_srid);
		$stmt->bind_param("i", $_POST['sid']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($srid);
		$stmt->fetch();

		header('Location: ' . APP_PATH_URL . '?page=editSubNarrative&id=' . $srid);
	}
?>

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Assign default values if variables don't exist
	$action = isset($_POST['action']) ? $_POST['action'] : "";
	$srid = isset($_POST['srid']) ? $_POST['srid'] : "";
	$sectionid = isset($_POST['sid']) ? $_POST['sid'] : "";
	$sectionName = isset($_POST['sectionName']) ? $_POST['sectionName'] : "";

	switch ($action) {
		case 0:	// Add new section

			$ins_section = "
				INSERT INTO ". TABLE_SECTION ." (srid, name)
				VALUES (?,?)
			";
			$stmt = $conn->prepare($ins_section);
			$stmt->bind_param("is", $srid, $sectionName);
			$stmt->execute();

			break;
		case 1: // Add new subsection
			break;
		case 2: // Edit section
			break;
		case 3: // Delete section
			break;
		default:
			echo 'ERROR: No valid action has been specified.';
			break;
	}

?>
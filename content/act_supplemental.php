<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Assign default values if variables don't exist
	$action = isset($_POST['action']) ? $_POST['action'] : "";
	$srid = isset($_POST['srid']) ? $_POST['srid'] : "";
	$sectionid = isset($_POST['sectionid']) ? $_POST['sectionid'] : "";
	$sectionName = isset($_POST['sectionName']) ? $_POST['sectionName'] : "";

	// Create AJAX response array
	$response = array();
	$response["errors"] = array();

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
		case 1: // Add new nested section

			$parentid = $sectionid; // This assignment is for clarity only

			$ins_nestedSection = "
				INSERT INTO ". TABLE_SECTION ." (srid, name, parent_id)
				VALUES (?,?,?)
			";
			$stmt = $conn->prepare($ins_nestedSection);
			$stmt->bind_param("isi", $srid, $sectionName, $parentid);
			$stmt->execute();

			break;
		case 2: // Edit section
			break;
		case 3: // Delete section
			break;
		default:
			array_push($response["errors"], "No valid action has been specified");
			break;
	}

	echo json_encode($response);
?>
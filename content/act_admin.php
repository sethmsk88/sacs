<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// First make sure newCR and newCS exist
	if (!isset($_POST['newCR'], $_POST['newCS'], $_POST['SRType'])) {
		exit; // Error! required variables missing
	}

	$json_response = array();
	$errors = array();

	// Convert SRType to DB format
	if ($_POST['SRType'] == 'CS') {
		$SRType = 's';
		$newSR = $_POST['newCS'];
	} else {
		$SRType = 'r';
		$newSR = $_POST['newCR'];
	}

	// check to see if there is a duplicate SR in the table
	// if no duplicate, insert SR into DB
	$sel_duplicate = "
		SELECT number
		FROM sacs.standard_requirement
		WHERE number = ?
	";
	$stmt = $conn->prepare($sel_duplicate);
	$stmt->bind_param("s", $newSR);
	$stmt->execute();
	$stmt->store_result();

	// If SR already exists, display error message
	if ($stmt->num_rows > 0) {
		array_push($errors, "This standard/requirement already exists.");
	} else {
		// Else, insert new SR
		$ins_sr = "
			INSERT INTO sacs.standard_requirement (number, sr_type)
			VALUES (?,?)
		";
		$stmt = $conn->prepare($ins_sr);
		$stmt->bind_param("ss", $newSR, $SRType);
		$stmt->execute();
		$stmt->store_result();
		$newID = $stmt->insert_id; // get id of newly inserted SR
	}

	// If there are any errors, add them to json response array
	if (empty($errors) == false) {
		// Add errors to json response array
		$json_response['errors'] = '<div class="alert alert-danger"><strong>Error(s)!</strong> ';
		foreach ($errors as $errorMsg) {
			$json_response['errors'] .= $errorMsg . '<br />';
		}
		$json_response['errros'] .= '</div>';
	} else {
		// Add the id of the new insert to the json response array
		$json_response['sr_id'] = $newID;
	}

	echo json_encode($json_response);
?>

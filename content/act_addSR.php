<?php
	require_once("../includes/globals.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . '/apps/shared/db_connect.php');

	// make sure type has been posted
	if (!isset($_POST['type'])) {
		echo '<div class="text-danger">Error! Direct access of this page is not allowed.</div>';
		exit;
	}

	if ($_POST['type'] == 's')
		$newSR = $_POST['newCS'];
	else if ($_POST['type'] == 'r')
		$newSR = $_POST['newCR'];

	// check to see if there is a duplicate SR in the table
	// if no duplicate, insert SR into DB
	$sel_duplicate = "
		SELECT number
		FROM " . TABLE_STANDARD_REQUIREMENT .  "
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
			INSERT INTO " . TABLE_STANDARD_REQUIREMENT . " (number, sr_type)
			VALUES (?,?)
		";
		$stmt = $conn->prepare($ins_sr);
		$stmt->bind_param("ss", $newSR, $_POST['type']);
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

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// If refURL was left blank, set to appendix page
	$refURL = $_POST['refURL'];
	if ($refURL == "") {
		$refURL =  APP_PATH_URL . '?page=appendix&id=' . $_POST['srid'];
	}

	// Make sure link begins with http or https
	$parsedURL = parse_url($refURL);
	if (empty($parsedURL['scheme'])) {
	    $refURL = 'http://' . ltrim($refURL, '/');
	}

	// Get highest refNum for this srid
	$sel_refNum = "
		SELECT refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE srid = ?
		ORDER BY refNum DESC
		LIMIT 1
	";
	$stmt = $conn->prepare($sel_refNum);
	$stmt->bind_param("i", $_POST['srid']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($highestRefNum);
	$stmt->fetch();

	$highestRefNum++; // increment highest reference number

	$ins_ref = "
		INSERT INTO " . TABLE_APPENDIX_LINK . " (srid, linkName, linkURL, refNum)
		VALUES (?,?,?,?)
	";
	$stmt = $conn->prepare($ins_ref);
	$stmt->bind_param("issi",
		$_POST['srid'],
		$_POST['refName'],
		$refURL,
		$highestRefNum);
	$stmt->execute();

	// Send AJAX response containing textarea id and new reference number
	$json_array = array();
	$json_array['textarea_id'] = $_POST['textarea_id'];
	$json_array['refNum'] = $highestRefNum;
	$json_array['refURL'] = $refURL;

	echo json_encode($json_array);
?>

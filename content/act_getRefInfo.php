<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Make sure required var exists
	if (!isset($_POST['linkID'])) {
		exit;
	}

	$sel_link = "
		SELECT linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_link);
	$stmt->bind_param("i", $_POST['linkID']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkName, $linkURL, $refNum);
	$stmt->fetch();

	$json_arr['linkName'] = $linkName;
	$json_arr['linkURL'] = $linkURL;
	$json_arr['refNum'] = $refNum;

	echo json_encode($json_arr);
?>

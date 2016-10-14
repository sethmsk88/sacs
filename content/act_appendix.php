<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// get ref num of first link
	$sel_refNum = "
		SELECT refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_refNum);
	$stmt->bind_param("i", $_POST['linkID_1']);
	$stmt->execute();
	
	$result = $stmt->get_result();
	$result_row = $result->fetch_assoc();
	$refNum_1 = $result_row['refNum'];

	// get ref num of second link
	$stmt->bind_param("i", $_POST['linkID_2']);
	$stmt->execute();
	
	$result = $stmt->get_result();
	$result_row = $result->fetch_assoc();
	$refNum_2 = $result_row['refNum'];

	// Swap rep nums
	$update_refNum = "
		UPDATE " . TABLE_APPENDIX_LINK . "
		SET refNum = ?
		WHERE appendix_link_id = ?
	";
	$stmt = $conn->prepare($update_refNum);
	$stmt->bind_param("ii", $refNum_2, $_POST['linkID_1']);
	$stmt->execute();

	$stmt->bind_param("ii", $refNum_1, $_POST['linkID_2']);
	$stmt->execute();
?>

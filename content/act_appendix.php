<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	function swapReferences($str, $ref1, $ref2) {
		$str = str_replace('['. $ref1 .']', '[A]', $str);
		$str = str_replace('['. $ref2 .']', '[B]', $str);
		$str = str_replace('[A]', '['. $ref2 .']', $str);
		$str = str_replace('[B]', '['. $ref1 .']', $str);

		return $str;
	}


	// Change Order
	if (isset($_POST['actionType']) && $_POST['actionType'] == 0) {

		// Get ref num of first link
		$sel_refNum = "
			SELECT refNum, srid
			FROM " . TABLE_APPENDIX_LINK . "
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($sel_refNum);
		$stmt->bind_param("i", $_POST['linkID_1']);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$result_row = $result->fetch_assoc();
		$refNum_1 = $result_row['refNum'];


		// Get ref num of second link
		$stmt->bind_param("i", $_POST['linkID_2']);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$result_row = $result->fetch_assoc();
		$refNum_2 = $result_row['refNum'];

		// Get standard/requirement id for these links
		$srid = $result_row['srid'];


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

		// Get standard/requirement
		$sel_sr = "
			SELECT descr, narrative, summary
			FROM " . TABLE_STANDARD_REQUIREMENT . "
			WHERE id = ?
		";
		$stmt = $conn->prepare($sel_sr);
		$stmt->bind_param("i", $srid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($descr, $narrative, $summary);
		$stmt->fetch();

		// Swap all occurrences of ref nums
		$descr = swapReferences($descr, $refNum_1, $refNum_2);
		$narrative = swapReferences($narrative, $refNum_1, $refNum_2);
		$summary = swapReferences($summary, $refNum_1, $refNum_2);
		
		// Update standard/requirement
		$update_sr = "
			UPDATE " . TABLE_STANDARD_REQUIREMENT . "
			SET descr = ?,
				narrative = ?,
				summary = ?
			WHERE id = ?
		";
		$stmt = $conn->prepare($update_sr);
		$stmt->bind_param("sssi", $descr, $narrative, $summary, $srid);
		$stmt->execute();

	// Delete Reference
	} else if (isset($_POST['actionType']) && $_POST['actionType'] == 1) {
		// get refNum of this ref
		$sel_refNum = "
			SELECT refNum, srid
			FROM " . TABLE_APPENDIX_LINK . "
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($sel_refNum);
		$stmt->bind_param("i", $_POST['linkID']);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$result_row = $result->fetch_assoc();
		$deleteRefNum = $result_row['refNum'];
		$srid = $result_row['srid'];

		// Decrement each of the SR's refNums that are greater than this one
		$update_refNum = "
			UPDATE " . TABLE_APPENDIX_LINK . "
			SET refNum = refNum - 1
			WHERE srid = ?
				AND refNum > ?
		";
		$stmt = $conn->prepare($update_refNum);
		$stmt->bind_param("ii", $srid, $deleteRefNum);
		$stmt->execute();

		// Delete this reference
		$del_ref = "
			DELETE FROM " . TABLE_APPENDIX_LINK . "
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($del_ref);
		$stmt->bind_param("i", $_POST['linkID']);
		$stmt->execute();

	// Edit Reference
	} else if (isset($_POST['actionType']) && $_POST['actionType'] == 2) {
		$update_ref = "
			UPDATE " . TABLE_APPENDIX_LINK . "
			SET linkName = ?,
				linkURL = ?
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($update_ref);
		$stmt->bind_param("ssi",
			$_POST['refName'],
			$_POST['refURL'],
			$_POST['linkID']);
		$stmt->execute();
	}
?>

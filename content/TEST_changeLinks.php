<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Replace reference links in text with new links
	function editReference($str, $ref, $url)
	{
		$newLink = '<a href="'. $url .'" target="_blank">['. $ref .']</a>';
		$refLink_pattern = "~<a\s[^>]*href=\"([^\"]*)\"[^>]*>\s*\[". $ref ."\]\s*</a>~siU";
		return preg_replace($refLink_pattern, $newLink, $str);
	}

	// The SRID of the SR we are currently editing
	$sridToEdit = 8;

	// get all SR text
	$sel_SR_text = "
		SELECT descr, narrative, summary
		FROM ". TABLE_STANDARD_REQUIREMENT ."
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_SR_text);
	$stmt->bind_param("i", $sridToEdit);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($descr, $narrative, $summary);
	$stmt->fetch();

	// get all supplemental text for SR
	$sel_supp = "
		SELECT id, body
		FROM ". TABLE_SECTION ."
		WHERE srid = ?
	";
	$stmt = $conn->prepare($sel_supp);
	$stmt->bind_param("i", $sridToEdit);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($sectionID, $sectionBody);

	// Store section bodies in array
	$sections = array();
	while ($stmt->fetch()) {
		$sections[$sectionID] = $sectionBody;
	}

	// get all file links
	$sel_fileLinks = "
		SELECT l.linkURL, l.refNum, f.file_upload_id
		FROM ". TABLE_APPENDIX_LINK ." AS l
		JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON lhf.appendix_link_id = l.appendix_link_id
		JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON lhf.file_upload_id = f.file_upload_id
		WHERE l.srid = ?
	";
	
	$stmt = $conn->prepare($sel_fileLinks);
	$stmt->bind_param('i', $sridToEdit);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkURL, $refNum, $fileID);

	// For each reference
	while ($stmt->fetch()) {
		// Replace link in text for each reference 
		$newRefURL = APP_GET_FILE_PAGE . '?fileid=' . $fileID;

		$descr = editReference($descr, $refNum, $newRefURL);
		$narrative = editReference($narrative, $refNum, $newRefURL);
		$summary = editReference($summary, $refNum, $newRefURL);

		// Replace link in each section body
		foreach ($sections as $sectionID => $sectionBody) {
			$sections[$sectionID] = editReference($sectionBody, $refNum, $newRefURL);
		}
	}


	// Update all text in DB for this SR
	$update_SR = "
		UPDATE ". TABLE_STANDARD_REQUIREMENT ."
		SET descr = ?,
			narrative = ?,
			summary = ?
		WHERE id = ?
	";

	if (!$stmt = $conn->prepare($update_SR)) {
		echo 'error preparing (' . $conn->errno .') ' . $conn->error;
		exit;
	}

	$stmt->bind_param("sssi", $descr, $narrative, $summary, $sridToEdit);
	$stmt->execute();

	// Update all section body text for this SR
	$update_section = "
		UPDATE ". TABLE_SECTION ."
		SET body = ?
		WHERE id = ?
	";
	$stmt = $conn->prepare($update_section);

	// Loop through all sections and update bodies
	foreach ($sections as $sectionID => $sectionBody) {
		$stmt->bind_param("si", $sectionBody, $sectionID);
		$stmt->execute();
	}


	// Output text AFTER updates
	/*echo '<p>' . $descr . '</p><br>';
	echo '<p>' . $narrative . '</p><br>';
	echo '<p>' . $summary . '</p><br>';
	foreach ($sections as $sectionBody) {
		echo '<p>' . $sectionBody . '</p><br>';
	}*/





?>

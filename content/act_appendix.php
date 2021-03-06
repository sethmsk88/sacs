<?php
	require_once("../includes/globals.php");
	require_once("../includes/functions.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	function swapReferences($str, $ref1, $ref2)
	{
		$str = str_replace('['. $ref1 .']', '[A]', $str);
		$str = str_replace('['. $ref2 .']', '[B]', $str);
		$str = str_replace('[A]', '['. $ref2 .']', $str);
		$str = str_replace('[B]', '['. $ref1 .']', $str);

		return $str;
	}

	// Delete reference link from text
	function deleteReference($str, $ref)
	{
		/*
			Match reference link with href attribute and other optional unknown attributes. Matching link name with the following format [#] where # = the reference number. Spaces on either side of the link name are also captured in case the User accidentally adds spaces to the link.
		*/
		$refLink_pattern = "~<a\s[^>]*href=\"([^\"]*)\"[^>]*>\s*\[". $ref ."\]\s*</a>~siU";
		return preg_replace($refLink_pattern, '', $str);
	}

	// Replace reference links in text with new links
	function editReference($str, $ref, $url)
	{
		global $json_arr; // TESTING

		$newLink = '<a href="'. $url .'" target="_blank">['. $ref .']</a>';
		$refLink_pattern = "~<a\s[^>]*href=\"([^\"]*)\"[^>]*>\s*\[". $ref ."\]\s*</a>~siU";

		$json_arr['test'] = preg_replace($refLink_pattern, $newLink, $str); // TESTING

		return preg_replace($refLink_pattern, $newLink, $str);
	}

	function decrementReference($str, $ref)
	{
		$decrementedRef = intval($ref) - 1;

		return str_replace('['. $ref .']', '['. $decrementedRef .']', $str);
	}

	// Check to see whether or not filename is only composed
	// of English characters, numbers, and (_-.) symbols.
	function checkUploadedFileName($fileName)
	{
		return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i", $fileName)) ? true : false);
	}

	// Upload file to application uploads directory
	// Returns array of errors and newly uploaded file id
	function uploadFile($fileObj)
	{
		global $VALID_UPLOAD_EXTENSIONS;

		$returnObj = array();
		$uploads_dir = 'uploads/';

		$errors = array();
		$fileName = $fileObj['name'];
		$fileSize = $fileObj['size'];
		$fileTmpName = $fileObj['tmp_name'];
		$fileType = $fileObj['type']; // (e.g. image/jpeg)
		$fileName_exploded = explode('.', $fileName);
		$fileExt = strtolower(end($fileName_exploded));

		$fileName = make_unique_filename($fileName, $uploads_dir);
		$filePath = APP_PATH . $uploads_dir . $fileName;

		$valid_extensions = array_keys($VALID_UPLOAD_EXTENSIONS);

		if (in_array($fileExt, $valid_extensions) === false) {
			$errMsg = "Invalid file type. Please choose a file with one of the following file types: ";
			$numExtensions = count($valid_extensions);
			foreach ($valid_extensions as $i => $extension) {
				$errMsg .= $extension;

				if ($i < $numExtensions - 1)
					$errMsg .= ", ";
			}
			array_push($errors, $errMsg);
		}

		// Make sure file is not too large
		$maxFileSize = 1024 * 1024 * 64; // 64MB
		if ($fileSize > $maxFileSize) {
			array_push($errors, "Max file size exceeded. File size must be less than 64MB");
		}

		// Make sure filename is not too long
		$maxFileNameLength = 250;
		if (mb_strlen($fileName, "UTF-8") > $maxFileNameLength) {
			array_push($errors, "File name is too long (max ". $maxFileNameLength ." characters)");
		}

		// If there are no errors, complete the upload by moving the temp file into the uploads directory
		if (empty($errors) === true) {
			move_uploaded_file($fileTmpName, $filePath);

			// insert the filename into the file upload table
			$file_id = insertFile($fileName, $fileExt);

			$returnObj['file_id'] = $file_id;
			$returnObj['linkURL'] = APP_PATH_URL . $uploads_dir . $fileName;
		}

		$returnObj['errors'] = $errors;
			
		return $returnObj;
	}

	// Insert file info into table
	function insertFile($fileName, $fileExt)
	{
		global $conn;

		$ins_file = "
			INSERT INTO " . TABLE_FILE_UPLOAD . " (fileName, fileExt, uploadDate)
			VALUES (?,?, NOW())
		";
		$stmt = $conn->prepare($ins_file);
		$stmt->bind_param("ss", $fileName, $fileExt);
		$stmt->execute();

		return $stmt->insert_id;
	}

	// Insert record into table associating a reference with a certain file
	function associateFile($linkID, $fileID)
	{
		global $conn;

		$ins_file_assoc = "
			INSERT INTO " . TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD . " (appendix_link_id, file_upload_id)
			VALUES (?,?)
		";
		$stmt = $conn->prepare($ins_file_assoc);
		$stmt->bind_param("ii", $linkID, $fileID);
		$stmt->execute();
	}

	// Make sure the URL begins with the protocol given as a parameter
	function enforceProtocol($url, $protocol)
	{
		$parsedURL = parse_url($url);
		if (empty($parsedURL['scheme']))
		    return 'http://' . ltrim($url, '/'); // prepend protocol to URL
		else
			return $url;
	}

	$uploadsDir = "../uploads/";


	////////////////////////////////
	//  ACTION TYPE: Change Order
	////////////////////////////////
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

		// Update reference numbers in title/description, narrative, and summary
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

		// Swap all occurrences of ref nums in this standard/requirement
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

		// Update reference numbers in sub-narrative sections
		$sel_section = "
			SELECT id, body
			FROM " . TABLE_SECTION . "
			WHERE srid = ?
		";
		$stmt = $conn->prepare($sel_section);
		$stmt->bind_param("i", $srid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sectionID, $sectionBody);

		// Update section
		$update_section = "
			UPDATE " . TABLE_SECTION . "
			SET body = ?
			WHERE id = ?
		";
		$stmt2 = $conn->prepare($update_section);

		// For each section in this standard/requirement, swap all occurrences of the ref nums being changed
		while ($stmt->fetch()) {
			$sectionBody = swapReferences($sectionBody, $refNum_1, $refNum_2);
			$stmt2->bind_param("si", $sectionBody, $sectionID);
			$stmt2->execute();
		}


	///////////////////////////////////
	//  ACTION TYPE: Delete Reference
	///////////////////////////////////
	} else if (isset($_POST['actionType']) && $_POST['actionType'] == 1) {
		// get info related to this ref
		$sel_refNum = "
			SELECT l.srid, l.appendix_link_id, l.refNum, lhf.file_upload_id, f.fileName
			FROM ". TABLE_APPENDIX_LINK ." AS l
			LEFT JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON l.appendix_link_id = lhf.appendix_link_id
			LEFT JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON lhf.file_upload_id = f.file_upload_id
			WHERE l.appendix_link_id = ?
		";
		if (!$stmt = $conn->prepare($sel_refNum)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("i", $_POST['linkID'])) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else {		
			$stmt->store_result();
			$stmt->bind_result($srid, $linkID, $deleteRefNum, $fileID, $fileName);
			$stmt->fetch();
		}

		// Get title/descr, narrative, and summary
		$sel_sr = "
			SELECT descr, narrative, summary
			FROM " . TABLE_STANDARD_REQUIREMENT . "
			WHERE id = ?
		";
		if (!$stmt = $conn->prepare($sel_sr)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("i", $srid)) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else {
			$stmt->store_result();
			$stmt->bind_result($descr, $narrative, $summary);
			$stmt->fetch();
		}

		// Get sections for this standard/requirement
		$sel_section = "
			SELECT id, body
			FROM " . TABLE_SECTION . "
			WHERE srid = ?
		";
		if (!$section_stmt = $conn->prepare($sel_section)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$section_stmt->bind_param("i", $srid)) {
			echo 'error: ' . $section_stmt->errno . ' - ' . $section_stmt->error;
		} else if (!$section_stmt->execute()) {
			echo 'error: ' . $section_stmt->errno . ' - ' . $section_stmt->error;
		} else {
			$section_stmt->store_result();
			$section_stmt->bind_result($sectionID, $sectionBody);
		}

		// Remove deleted reference link from title/descr, narrative, and summary
		$descr = deleteReference($descr, $deleteRefNum);
		$narrative = deleteReference($narrative, $deleteRefNum);
		$summary = deleteReference($summary, $deleteRefNum);

		// Remove deleted reference link from all sections of this SR and store sectionBodies in an associative array
		$sectionBody_arr = array();
		while ($section_stmt->fetch()) {
			$sectionBody_arr[$sectionID] = deleteReference($sectionBody, $deleteRefNum);
		}

		// Get all references greater than the deleted reference
		$sel_greaterRefNums = "
			SELECT refNum
			FROM " . TABLE_APPENDIX_LINK . "
			WHERE srid = ?
				AND refNum > ?
			ORDER BY refNum ASC
		";
		if (!$stmt = $conn->prepare($sel_greaterRefNums)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("ii", $srid, $deleteRefNum)) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else {
			$stmt->store_result();
			$stmt->bind_result($greaterRefNum);
		}

		// For each reference greater than the deleted reference, decrement the refNum within the text of the title/descr, narrative, and summary
		while ($stmt->fetch()) {
			$descr = decrementReference($descr, $greaterRefNum);
			$narrative = decrementReference($narrative, $greaterRefNum);
			$summary = decrementReference($summary, $greaterRefNum);

			// Iterate through sections and decrement references
			foreach ($sectionBody_arr as $sectionID => $sectionBody) {
				$sectionBody_arr[$sectionID] = decrementReference($sectionBody, $greaterRefNum);
			}
		}

		// Decrement each of the SR's refNums that are greater than this one
		$update_refNum = "
			UPDATE " . TABLE_APPENDIX_LINK . "
			SET refNum = refNum - 1
			WHERE srid = ?
				AND refNum > ?
		";
		if (!$stmt = $conn->prepare($update_refNum)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("ii", $srid, $deleteRefNum)) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		}

		// Delete this reference
		$del_ref = "
			DELETE FROM " . TABLE_APPENDIX_LINK . "
			WHERE appendix_link_id = ?
		";
		if (!$stmt = $conn->prepare($del_ref)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("i", $_POST['linkID'])) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		}

		// Delete file and file info if link has a file associated with it
		if (!is_null($fileID)) {
			// Delete the link-file association record
			$del_link_file_assoc = "
				DELETE FROM ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ."
				WHERE appendix_link_id = ?
					AND file_upload_id = ?
			";
			$stmt = $conn->prepare($del_link_file_assoc);
			$stmt->bind_param('ii', $linkID, $fileID);
			$stmt->execute();

			// Delete the file record
			$del_file = "
				DELETE FROM ". TABLE_FILE_UPLOAD ."
				WHERE file_upload_id = ?
			";
			$stmt = $conn->prepare($del_file);
			$stmt->bind_param('i', $fileID);
			$stmt->execute();

			// Delete the file from the server
			delete_file_from_server($fileName, $uploadsDir);
		}
	
		// Update standard/requirement
		$update_sr = "
			UPDATE " . TABLE_STANDARD_REQUIREMENT . "
			SET descr = ?,
				narrative = ?,
				summary = ?
			WHERE id = ?
		";
		if (!$stmt = $conn->prepare($update_sr)) {
			echo 'error: ' . $conn->errno . ' - ' . $conn->error;
		} else if (!$stmt->bind_param("sssi", $descr, $narrative, $summary, $srid)) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		} else if (!$stmt->execute()) {
			echo 'error: ' . $stmt->errno . ' - ' . $stmt->error;
		}

		// Update sections
		$update_section = "
			UPDATE " . TABLE_SECTION . "
			SET body = ?
			WHERE id = ?
		";
		$updateSection_stmt = $conn->prepare($update_section);

		// For each section in this standard/requirement, swap all occurrences of the ref nums being changed
		foreach ($sectionBody_arr as $sectionID => $sectionBody) {
			$updateSection_stmt->bind_param("si", $sectionBody, $sectionID);
			$updateSection_stmt->execute();
		}


	////////////////////////////////
	//  ACTION TYPE: Edit Reference
	////////////////////////////////
	} else if (isset($_POST['actionType']) && $_POST['actionType'] == 2) {

		$json_arr = array(); // ajax response object

		// get refNum of this ref
		$sel_refNum = "
			SELECT refNum, srid
			FROM " . TABLE_APPENDIX_LINK . "
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($sel_refNum);
		$stmt->bind_param("i", $_POST['refLinkID']);
		$stmt->execute();
		$result = $stmt->get_result();
		$result_row = $result->fetch_assoc();
		$editLinkRefNum = $result_row['refNum']; // ref num of the link being edited
		$srid = $result_row['srid'];

		// It was decided that blank references should point to the Appendix page
		$refURL = $_POST['refURL'];
		if ($refURL == "") {
			$refURL =  APP_PATH_URL . '?page=appendix&id=' . $srid;
		}

		// Make sure link begins with http or https
		$refURL = enforceProtocol($refURL, 'http://');

		// Get title/descr, narrative, and summary
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

		// if attaching a file
		if ($_FILES['fileToUpload']['name'] !== '') {
			$uploadResultObj = uploadFile($_FILES['fileToUpload']);
			
			// if there were errors during the upload
			if (!empty($uploadResultObj['errors'])) {
				$json_arr['errors'] = $uploadResultObj['errors'];
			} else {
				associateFile($_POST['refLinkID'], $uploadResultObj['file_id']);
				$refURL = $uploadResultObj['linkURL']; // assign new url for uploaded file
			}
		}

		// Update all references within the text with the changes
		$descr = editReference($descr, $editLinkRefNum, $refURL);
		$narrative = editReference($narrative, $editLinkRefNum, $refURL);
		$summary = editReference($summary, $editLinkRefNum, $refURL);

		// Update the link name and link URL for this reference
		$update_ref = "
			UPDATE " . TABLE_APPENDIX_LINK . "
			SET linkName = ?,
				linkURL = ?
			WHERE appendix_link_id = ?
		";
		$stmt = $conn->prepare($update_ref);
		$stmt->bind_param("ssi",
			$_POST['refName'],
			$refURL,
			$_POST['refLinkID']);
		$stmt->execute();

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

		// Update reference numbers in sub-narrative sections
		$sel_section = "
			SELECT id, body
			FROM " . TABLE_SECTION . "
			WHERE srid = ?
		";
		$stmt = $conn->prepare($sel_section);
		$stmt->bind_param("i", $srid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sectionID, $sectionBody);

		// Update section
		$update_section = "
			UPDATE " . TABLE_SECTION . "
			SET body = ?
			WHERE id = ?
		";
		$stmt2 = $conn->prepare($update_section);

		// For each section in this standard/requirement, swap all occurrences of the ref nums being changed
		while ($stmt->fetch()) {
			$sectionBody = editReference($sectionBody, $editLinkRefNum, $refURL);
			$stmt2->bind_param("si", $sectionBody, $sectionID);
			$stmt2->execute();
		}

		// Respond with json error messages if any
		echo json_encode($json_arr);


	////////////////////////////////
	//  ACTION TYPE: Remove File
	////////////////////////////////
	} else if (isset($_POST['actionType']) && $_POST['actionType'] == 3) {

		// Delete the file from the server
		$sel_filePath = "
			SELECT fileName
			FROM ". TABLE_FILE_UPLOAD ."
			WHERE file_upload_id = ?
		";
		$stmt = $conn->prepare($sel_filePath);
		$stmt->bind_param("i", $_POST['fileID']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($fileName);
		$stmt->fetch();

		delete_file_from_server($fileName, $uploadsDir);

		// Delete file association from table
		$del_file_assoc = "
			DELETE FROM ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ."
			WHERE file_upload_id = ?
				AND appendix_link_id = ?
		";
		$stmt = $conn->prepare($del_file_assoc);
		$stmt->bind_param("ii", $_POST['fileID'], $_POST['linkID']);
		$stmt->execute();

		// Delete file from table
		$del_file = "
			DELETE FROM ". TABLE_FILE_UPLOAD ."
			WHERE file_upload_id = ?
		";
		$stmt = $conn->prepare($del_file);
		$stmt->bind_param("i", $_POST['fileID']);
		$stmt->execute();

		// Delete the linkURL from table
		$update_linkURL = "
			UPDATE ". TABLE_APPENDIX_LINK ."
			SET linkURL = ?
			WHERE appendix_link_id = ?
		";
		$emptyURL = "";
		$stmt = $conn->prepare($update_linkURL);
		$stmt->bind_param("si", $emptyURL, $_POST['linkID']);
		$stmt->execute();
	}
?>

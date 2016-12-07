<?php
	require_once("../includes/globals.php");
	require_once("../includes/functions.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	/*
		Get the hightest reference number associated with the standard/requirement identified by the srid parameter, increment the reference number by 1, then return the new highest reference number.
	*/
	function getNewReferenceNumber($srid) {
		global $conn;

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

		return ++$highestRefNum;
	}

	/*
		Insert a new entry in the appendix link table
	*/
	function insertReference($srid, $refName, $refURL, $highestRefNum) {
		global $conn;

		$ins_ref = "
			INSERT INTO " . TABLE_APPENDIX_LINK . " (srid, linkName, linkURL, refNum)
			VALUES (?,?,?,?)
		";
		$stmt = $conn->prepare($ins_ref);
		$stmt->bind_param("issi",
			$srid,
			$refName,
			$refURL,
			$highestRefNum);
		$stmt->execute();

		// return id of newly inserted reference
		return $stmt->insert_id;
	}

	// Upload file to application uploads directory
	// Returns array of errors and newly uploaded file id
	function uploadFile($fileObj) {
		global $VALID_UPLOAD_EXTENSIONS;

		$returnObj = array();
		$uploads_dir = 'uploads/';

		$errors = array();
		$fileName = $fileObj['name'];
		$fileSize = $fileObj['size'];
		$fileTmpName = $fileObj['tmp_name'];
		$fileType = $fileObj['type'];
		$fileName_exploded = explode('.', $fileName);
		$fileExt = strtolower(end($fileName_exploded));
		
		$fileName = make_unique_filename($fileName, $uploads_dir);
		$filePath = APP_PATH . $uploads_dir . $fileName;

		if (in_array($fileExt, $VALID_UPLOAD_EXTENSIONS) === false) {
			$errMsg = "Invalid file type. Please choose a file with one of the following file types: ";
			$numExtensions = count($VALID_UPLOAD_EXTENSIONS);
			foreach ($VALID_UPLOAD_EXTENSIONS as $i => $extension) {
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
		if (strlen($fileName) > $maxFileNameLength) {
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
	function insertFile($fileName, $fileExt) {
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
	function associateFile($link_id, $file_id) {
		global $conn;

		$ins_file_assoc = "
			INSERT INTO " . TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD . " (appendix_link_id, file_upload_id)
			VALUES (?,?)
		";
		$stmt = $conn->prepare($ins_file_assoc);
		$stmt->bind_param("ii", $link_id, $file_id);
		$stmt->execute();
	}

	// Make sure the URL begins with the protocol given as a parameter
	function enforceProtocol($url, $protocol) {
		$parsedURL = parse_url($url);
		if (empty($parsedURL['scheme']))
		    return 'http://' . ltrim($url, '/'); // prepend protocol to URL
		else
			return $url;
	}

	$json_array = array(); // response array

	// Check isset refChoice and newRefType variables
	$refChoice = isset($_POST['refChoice']) ? $_POST['refChoice'] : ""; // if $_POST['refChoice'] isset, then store its value in $refChoice, else store "" in $refChoice
	$newRefType = isset($_POST['newRefType']) ? $_POST['newRefType'] : "";

	// If inserting a URL reference
	if ($refChoice === "1" && $newRefType === "0") {

		// If refURL was left blank, set reference to appendix page
		$refURL = $_POST['refURL'];
		if ($refURL == "") {
			$refURL =  APP_PATH_URL . '?page=appendix&id=' . $_POST['srid'];
		}

		// Make sure link begins with http or https
		$refURL = enforceProtocol($refURL, 'http://');

		$highestRefNum = getNewReferenceNumber($_POST['srid']);
		insertReference($_POST['srid'], $_POST['refName'], $refURL, $highestRefNum);

		// Add values to AJAX response object
		$json_array['refNum'] = $highestRefNum;
		$json_array['refURL'] = $refURL;

	// Else, if inserting an attached file as a reference
	} else if ($refChoice === "1" && $newRefType === "1") {

		// if a FormData object was posted to this page
		if (isset($_FILES['fileToUpload'])) {
			$uploadResultObj = uploadFile($_FILES['fileToUpload']);

			// if there were errors during the upload
			if (!empty($uploadResultObj['errors'])) {
				$json_array['errors'] = $uploadResultObj['errors'];
			} else {

				// insert new refLink into reference table
				$highestRefNum = getNewReferenceNumber($_POST['srid']);
				$refURL = ""; // blank for attached files
				$link_id = insertReference($_POST['srid'], $_POST['refName'], $refURL, $highestRefNum);
				associateFile($link_id, $uploadResultObj['file_id']);

				$json_array['fileid'] = $uploadResultObj['file_id'];
				$json_array['refNum'] = $highestRefNum;
			}
		}
	}

	/*
		This value is needed when the insert ref function uses this action file
		The value is used to determin where to insert the new reference link
	*/
	$textarea_id = isset($_POST['textarea_id']) ? $_POST['textarea_id'] : "";
	$json_array['textarea_id'] = $textarea_id;

	echo json_encode($json_array);
?>

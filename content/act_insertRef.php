<?php
	require_once("../includes/globals.php");
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

	function insertFile($fileName, $filePath, $fileExt) {
		global $conn;

		$ins_file = "
			INSERT INTO " . TABLE_FILE_UPLOAD . " (fileName, filePath, fileExt, uploadDate)
			VALUES (?,?,?, NOW())
		";
		if (!$stmt = $conn->prepare($ins_file)) {
			echo 'error ('. $conn->errno .') '. $conn->error;
		}
		$stmt->bind_param("sss", $fileName, $filePath, $fileExt);
		$stmt->execute();

		return $stmt->insert_id;
	}

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

	$json_array = array(); // response array

	// Check isset refChoice and newRefType variables
	$refChoice = isset($_POST['refChoice']) ? $_POST['refChoice'] : ""; // if $_POST['refChoice'] isset, then store its value in $refChoice, else store "" in $refChoice
	$newRefType = isset($_POST['newRefType']) ? $_POST['newRefType'] : "";

	// If refURL was left blank, set reference to appendix page
	$refURL = $_POST['refURL'];
	if ($refURL == "") {
		$refURL =  APP_PATH_URL . '?page=appendix&id=' . $_POST['srid'];
	}

	// TODO: Make sure a ref name was provided

	// If inserting a URL reference
	if ($refChoice === "1" && $newRefType === "0") {

		// Make sure link begins with http or https
		$parsedURL = parse_url($refURL);
		if (empty($parsedURL['scheme'])) {
		    $refURL = 'http://' . ltrim($refURL, '/');
		}

		$highestRefNum = getNewReferenceNumber($_POST['srid']);
		insertReference($_POST['srid'], $_POST['refName'], $refURL, $highestRefNum);

		// Send AJAX response containing textarea id and new reference number
		$json_array['textarea_id'] = $_POST['textarea_id'];
		$json_array['refNum'] = $highestRefNum;
		$json_array['refURL'] = $refURL;

	// Else, if inserting an attached file as a reference
	} else if ($refChoice === "1" && $newRefType === "1") {

		// TODO: Make sure a file was attached

		// if a FormData object was posted to this page
		if (isset($_FILES['fileToUpload'])) {
			$uploads_dir = '../uploads/';

			// Start timer
			$timerStart = microtime(true);

			$error = array();
			$fileName = $_FILES['fileToUpload']['name'];
			$fileSize = $_FILES['fileToUpload']['size'];
			$fileTmpName = $_FILES['fileToUpload']['tmp_name'];
			$fileType = $_FILES['fileToUpload']['type'];
			$fileName_exploded = explode('.', $fileName);
			$fileExt = strtolower(end($fileName_exploded));
			
			// Append timestamp to filename
			$timeStamp = date("YmdHis"); // 1/2/2016 1:05:12pm = 20160102130512
			$fileName = $timeStamp . '_' . $fileName_exploded[0] . '.' . $fileExt;
			$filePath = $uploads_dir . $fileName;

			// Check to see if extension is valid
			$extensions = array("pdf", "doc", "docx", "xls", "xlsx", "csv", "ppt", "pptx", "pub");
			if (in_array($fileExt, $extensions) === false) {
				$errMsg = "Invalid file type. Please choose a file with one of the following file types: ";
				$numExtensions = count($extensions);
				foreach ($extensions as $i => $extension) {
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

			// If upload failed respond with error message(s)
			if (empty($errors) === false) {
				$json_array['errors'] = $errors;

			// Else, 
			} else {
				// move temp uploaded file to uploads directory
				move_uploaded_file($fileTmpName, $filePath);

				// insert new refLink into reference table
				$highestRefNum = getNewReferenceNumber($_POST['srid']);
				$link_id = insertReference($_POST['srid'], $_POST['refName'], $refURL, $highestRefNum);
				
				// insert the filename into the file upload table
				$file_id = insertFile($fileName, $filePath, $fileExt);

				// insert the link/file association into table
				associateFile($link_id, $file_id);
			}


		}

	}

	echo json_encode($json_array);
?>

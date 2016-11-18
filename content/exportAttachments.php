<?php
	require_once "../includes/globals.php";
	require_once "../includes/functions.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	// Remove timestampe from beginning of filename
	// Return modified filename
	function removeTimestamp($fileName) {
		$fileName_exploded = explode('_', $fileName);
		array_shift($fileName_exploded); // Remove first item from array
		return implode('_', $fileName_exploded);
	}

	// Uploads Directory Relative Path
	$uploadDirRelPath = "../uploads/";

	// Get SR type and SR number
	$sel_sr = "
		SELECT number, sr_type
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srNum, $srType);
	$stmt->fetch();

	// Get files for this SR
	$sel_files = "
		SELECT l.appendix_link_id, f.file_upload_id, f.fileName
		FROM ". TABLE_APPENDIX_LINK ." l
		JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." lhf
		ON l.appendix_link_id = lhf.appendix_link_id
		JOIN ". TABLE_FILE_UPLOAD ." f
		ON lhf.file_upload_id = f.file_upload_id
		WHERE l.srid = ?
	";
	$stmt = $conn->prepare($sel_files);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkID, $fileID, $fileName);

	// Create ZIP file name
	if ($srType === 's')
		$srPrefix = 'CS_';
	else
		$srPrefix = 'CR_';

	$tmpDir = ini_get('upload_tmp_dir');
	$zipFileName = $srPrefix . str_replace('.', '_', $srNum) . '_attachments.zip';
	$zipFilePath = $tmpDir . '/' . $zipFileName;
	$zip = new ZipArchive();
	
	// Create and open ZIP file
	if ($zip->open($zipFilePath, ZipArchive::OVERWRITE) !== true) {
		exit("Cannot open (" . $zipFilePath . ")\n");
	}

	// Add all attachments to ZIP file
	while ($stmt->fetch()) {
		$filePath = $uploadDirRelPath . $fileName;
		$fileName = removeTimestamp($fileName);

		if (file_exists($filePath) && is_readable($filePath)) {
			$zip->addFile($filePath, $fileName);
		} else {
			exit('Error: File not added ("'. $filePath .'")');
		}
	}
	$zip->close();

	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename=' . $zipFileName);
	header('Content-Length: '. filesize($zipFilePath));
	readfile($zipFilePath);
?>

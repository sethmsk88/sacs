<?php
	require_once "../includes/globals.php";
	require_once "../includes/functions.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	function convertDBTableToArray($tableName) {
		global $conn;

		$sql = "
			SELECT *
			FROM ". $tableName ."
		";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->get_result();

		$table_array = array();
		while ($row = $result->fetch_assoc()) {
			$table_array[] = $row;
		}

		return $table_array;
	}

	chdir("../portable_files/"); // change working directory to portable_files directory

	$dateTimeStr = date("YmdHis");

	$tmp_dir_name = $dateTimeStr . "_sacs_portable"; // make dir name unique to prevent naming conflicts
	$tmp_dir_path = sys_get_temp_dir() . "/" . $tmp_dir_name . "/";
	mkdir($tmp_dir_path);

	// Create JSON file containing contents of sacs database
	$sacs_array = array();

	$appendix_link_array = convertDBTableToArray(TABLE_APPENDIX_LINK);
	$appendix_link_has_file_upload_array = convertDBTableToArray(TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD);
	$file_upload_array = convertDBTableToArray(TABLE_FILE_UPLOAD);
	$section_array = convertDBTableToArray(TABLE_SECTION);
	$standard_requirement_array = convertDBTableToArray(TABLE_STANDARD_REQUIREMENT);

	$sacs_array["appendix_link"] = $appendix_link_array;
	$sacs_array["appendix_link_has_file_upload"] = $appendix_link_has_file_upload_array;
	$sacs_array["file_upload"] = $file_upload_array;
	$sacs_array["section"] = $section_array;
	$sacs_array["standard_requirement"] = $standard_requirement_array;

	// Write JSON to file
	$fp = fopen($tmp_dir_path . "sacs_db.json", "w");
	fwrite($fp, json_encode($sacs_array));
	fclose($fp);


	// copy resource files to tmp directory
	$resources_dir_path = $tmp_dir_path . "resources/";
	mkdir($resources_dir_path);
	$resources_array = scandir("./resources");
	foreach ($resources_array as $file) {
		if (is_file("./resources/" . $file)) {
			copy("./resources/" . $file, $resources_dir_path . $file);
		}
	}

	// copy image files to tmp directory
	$img_dir_path = $tmp_dir_path . "img/";
	mkdir($img_dir_path);
	$img_array = scandir("../img");
	foreach ($img_array as $file) {
		if (is_file("../img/" . $file)) {
			copy("../img/" . $file, $img_dir_path . $file);
		}
	}

	// copy uploaded files to tmp directory
	$uploads_dir_path = $tmp_dir_path . "uploads/";
	mkdir($uploads_dir_path);
	$uploads_array = scandir("../uploads");
	foreach ($uploads_array as $file) {
		if (is_file("../uploads/" . $file)) {
			copy("../uploads/" . $file, $uploads_dir_path . $file);
		}
	}

	// Get file contents for header and footer
	$index_header_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_index_header.php");
	$index_footer_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_index_footer.php");

	// create index.html (include view1.php)
	$index_fp = fopen($tmp_dir_path . "/index.html", "w");
	$view1_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_view1.php");
	fwrite($index_fp, $index_header_fileContents);
	fwrite($index_fp, $view1_fileContents);
	fwrite($index_fp, $index_footer_fileContents);
	fclose($index_fp);


	// create narrative.html
	$narrative_fp = fopen($tmp_dir_path . "/narrative.html", "w");
	$narrative_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_narrative.php");
	fwrite($narrative_fp, $index_header_fileContents);
	fwrite($narrative_fp, $narrative_fileContents);
	fwrite($narrative_fp, $index_footer_fileContents);
	fclose($narrative_fp);
	
?>

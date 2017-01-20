<?php
	require_once "../includes/globals.php";
	require_once "../includes/functions.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	chdir("../portable_files/"); // change working directory to portable_files directory

	$dateTimeStr = date("YmdHis");

	$tmp_dir_name = $dateTimeStr . "_sacs_portable"; // make dir name unique to prevent naming conflicts
	$tmp_dir_path = sys_get_temp_dir() . "/" . $tmp_dir_name . "/";
	mkdir($tmp_dir_path);

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
	// $index_fp = fopen($tmp_dir_path . "/index.html", "w");

	// Create view1.html
	$view1_fp = fopen($tmp_dir_path . "view1.html", "w");
	$view1_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_view1.php");
	fwrite($view1_fp, $index_header_fileContents);
	fwrite($view1_fp, $view1_fileContents);
	fwrite($view1_fp, $index_footer_fileContents);
	fclose($view1_fp);

	// Get list of all SRIDs, then loop over the view1 generation code, generating all possible files
	$sel_sr = "
		SELECT ID
		FROM ". TABLE_STANDARD_REQUIREMENT ."
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srid);

	// create narrative.html
	while ($stmt->fetch()) {
		$filename = "narrative_" . $srid . ".html";
		$narrative_fp = fopen($tmp_dir_path . $filename, "w");
		$narrative_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_narrative.php?id=" . $srid);
		fwrite($narrative_fp, $index_header_fileContents);
		fwrite($narrative_fp, $narrative_fileContents);
		fwrite($narrative_fp, $index_footer_fileContents);
		fclose($narrative_fp);
	}


	/*$narrative_fp = fopen($tmp_dir_path . "/narrative.html", "w");
	$narrative_fileContents = file_get_contents(APP_PATH_URL . "portable_files/portable_narrative.php?id=1");
	fwrite($narrative_fp, $index_header_fileContents);
	fwrite($narrative_fp, $narrative_fileContents);
	fwrite($narrative_fp, $index_footer_fileContents);
	fclose($narrative_fp);*/
	
?>

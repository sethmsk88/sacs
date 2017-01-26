<?php
	require_once "../includes/globals.php";
	require_once "../includes/functions.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';

	chdir("../portable_files/"); // change working directory to portable_files directory

	$dateTimeStr = date("YmdHis");

	$tmp_dir_name = $dateTimeStr . "_sacs_portable"; // make dir name unique to prevent naming conflicts
	$tmp_dir_path = ini_get('upload_tmp_dir') . "/" . $tmp_dir_name . "/";
	mkdir($tmp_dir_path);
	mkdir($tmp_dir_path . "app/"); // create app directory for all of the app files

	// copy the click to run file to tmp directory
	$runFilename = "CLICK TO RUN.html";
	copy($runFilename, $tmp_dir_path . $runFilename);

	// copy resource files to tmp directory
	$resources_dir_path = $tmp_dir_path . "app/resources/";
	mkdir($resources_dir_path);
	$resources_array = scandir("./resources");
	foreach ($resources_array as $file) {
		if (is_file("./resources/" . $file)) {
			copy("./resources/" . $file, $resources_dir_path . $file);
		}
	}

	// copy image files to tmp directory
	$img_dir_path = $tmp_dir_path . "app/img/";
	mkdir($img_dir_path);
	$img_array = scandir("../img");
	foreach ($img_array as $file) {
		if (is_file("../img/" . $file)) {
			copy("../img/" . $file, $img_dir_path . $file);
		}
	}

	// copy uploaded files to tmp directory
	$uploads_dir_path = $tmp_dir_path . "app/uploads/";
	mkdir($uploads_dir_path);
	$uploads_array = scandir("../uploads");
	foreach ($uploads_array as $file) {
		if (is_file("../uploads/" . $file)) {
			copy("../uploads/" . $file, $uploads_dir_path . $file);
		}
	}

	// Get file contents for header and footer
	$index_header_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_index_header.php");
	$index_footer_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_index_footer.php");

	// Create view1.html
	$view1_fp = fopen($tmp_dir_path . "app/view1.html", "w");
	$view1_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_view1.php");
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

	// store all SRIDs in array
	$srid_array = array();
	while ($stmt->fetch()) {
		$srid_array[] = $srid;
	}

	// create narrative.html
	foreach ($srid_array as $srid) {
		$filename = "narrative_" . $srid . ".html";
		$narrative_fp = fopen($tmp_dir_path . "app/" . $filename, "w");
		$narrative_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_narrative.php?id=" . $srid);
		fwrite($narrative_fp, $index_header_fileContents);
		fwrite($narrative_fp, $narrative_fileContents);
		fwrite($narrative_fp, $index_footer_fileContents);
		fclose($narrative_fp);
	}

	// create subNarrative.html
	foreach ($srid_array as $srid) {
		$filename = "subNarrative_" . $srid . ".html";
		$subNarrative_fp = fopen($tmp_dir_path . "app/" . $filename, "w");
		$subNarrative_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_subNarrative.php?id=" . $srid);
		fwrite($subNarrative_fp, $index_header_fileContents);
		fwrite($subNarrative_fp, $subNarrative_fileContents);
		fwrite($subNarrative_fp, $index_footer_fileContents);
		fclose($subNarrative_fp);
	}

	// create appendix.html
	foreach ($srid_array as $srid) {
		$filename = "appendix_" . $srid . ".html";
		$appendix_fp = fopen($tmp_dir_path . "app/" . $filename, "w");
		$appendix_fileContents = file_get_contents(APP_PATH_IP_URL . "portable_files/portable_appendix.php?id=" . $srid);
		fwrite($appendix_fp, $index_header_fileContents);
		fwrite($appendix_fp, $appendix_fileContents);
		fwrite($appendix_fp, $index_footer_fileContents);
		fclose($appendix_fp);
	}

	/***********************/
	/*   CREATE ZIP FILE   */
	/***********************/
	$tmp_dir_name_exploded = explode("_", $tmp_dir_name);
	array_shift($tmp_dir_name_exploded); // remove timestamp (1st element) from array
	$zipFilename = implode("_", $tmp_dir_name_exploded) . ".zip";
	$zipFilePath = ini_get('upload_tmp_dir') . "/" . $zipFilename;
	$zip = new ZipArchive();

	// Create and open ZIP file
	if ($zip->open($zipFilePath, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE) !== true) {
		exit("Cannot open (" . $zipFilePath . ")\n");
	}

	// Create empty directories in ZIP archive
	$zip->addEmptyDir('app/');
	$zip->addEmptyDir('app/img/');
	$zip->addEmptyDir('app/resources/');
	$zip->addEmptyDir('app/uploads/');

	// copy all files from tmp directory to zip archive
	$tmp_dir_array = scandir($tmp_dir_path);
	$app_array = scandir($tmp_dir_path . 'app/');
	$img_array = scandir($tmp_dir_path . 'app/img');
	$resources_array = scandir($tmp_dir_path . 'app/resources');
	$uploads_array = scandir($tmp_dir_path . 'app/uploads');

	// Format of all_dirs_array:
	// [[DIR_PATH_0, DIR_ARRAY_0], [DIR_PATH_1, DIR_ARRAY_1], ...]
	$all_dirs_array = array();
	$all_dirs_array[] = array('', $tmp_dir_array);
	$all_dirs_array[] = array('app/', $app_array);
	$all_dirs_array[] = array('app/img/', $img_array);
	$all_dirs_array[] = array('app/resources/', $resources_array);
	$all_dirs_array[] = array('app/uploads/', $uploads_array);

	$fileCount = 0;
	foreach ($all_dirs_array as $dir_array) {
		$dir_path = $dir_array[0];

		foreach ($dir_array[1] as $file) {
			$file_path = $dir_path . $file;

			if (is_file($tmp_dir_path . $file_path)) {
				if (file_exists($tmp_dir_path . $file_path) && is_readable($tmp_dir_path . $file_path)) {

					$zip->addFile($tmp_dir_path . $file_path, $file_path);
					$fileCount++;

					// close and reopen ZIP archive after every 5 files to prevent memory overload
					if ( ($fileCount % 5) == 0) {
						$zip->close();
						$zip->open($zipFilePath);
					}
				} else {
					exit('Error: File not added ("'. $file_path .'")');
				}
			}
		}
	}
	$zipFilePath = $zip->filename;
	$zip->close();

	// Download ZIP file
	// Open file
	$file = @fopen($zipFilePath,"rb");
	$fileSize = fstat($file)['size'];

	// Force download
	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=\"$zipFilename\"");

	// IE6-7 compatibility
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

	// output the file in chunks to prevent potential memory problems when dealing with large files
	set_time_limit(0); // override default time limit - no time limit imposed	
	while(!feof($file))
	{
		print(@fread($file, 1024*8));
		ob_flush();
		flush();
	}
	fclose($file);
?>

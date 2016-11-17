<?php
/* WARNING!!! */
/* Do NOT run this file without changing the foreach loop if statement */
/* Otherwise, you may lose files you did not want to delete */
/*
	This developer's script has been intentionally left commented out. It is meant to be manually updated and used by the developer to delete specific files from the uploads directory.
*/
	
/****************************
	// Save current directory
	$originalDir = getcwd();

	// Change working directory to uploads directory
	chdir('../uploads/');

	// Get contents of current directory
	$files = scandir('./');

	echo 'Files Deleted: <br>';

	// Delete all files in files array that have indexes lower than 29
	// I did this because, I was getting rid of old files
	foreach ($files as $i => $file) {
		if ($i <= 28 && is_file($file)) {
			echo $file . '<br>';
			// unlink($file); ONLY UNCOMMENT THIS WHEN READY TO DELETE FILES
		}
	}

	// Change working directory back to original directory
	chdir($originalDir);
******************************/
?>

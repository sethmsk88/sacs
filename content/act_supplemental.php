<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';


	if (isset($_POST['action']) && $_POST['action'] == 0) {
		
	} else {
		echo 'ERROR: No action has been specified.';
	}

?>
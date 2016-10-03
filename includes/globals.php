<?php
	define("APP_DEV_MODE", true);
	define("APP_NAME", "SACS Accreditation");
	define("APP_PATH",  $_SERVER['DOCUMENT_ROOT'] . "/bootstrap/apps/sacs/");
    define("APP_PATH_URL", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/sacs/");
    define("APP_HOMEPAGE", "view1");

    if (APP_DEV_MODE == true) {
    	$TABLE_SECTION = "sacs.dev_section";
    	$TABLE_STANDARD_REQUIREMENT = "sacs.dev_standard_requirement";
    } else {
    	$TABLE_SECTION = "sacs.section";
    	$TABLE_STANDARD_REQUIREMENT = "sacs.standard_requirement";
    }
?>

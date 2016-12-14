<?php
	define("APP_DEV_MODE", true); // This should be set to true while developing
	define("APP_NAME", "SACS Accreditation");
	define("APP_PATH",  $_SERVER['DOCUMENT_ROOT'] . "/bootstrap/apps/sacs/");
    define("APP_PATH_URL", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/sacs/");
    define("APP_HOMEPAGE", "view1");
    define("APP_GET_FILE_PAGE", "http://". $_SERVER['HTTP_HOST'] ."/bootstrap/apps/sacs/content/get_file.php");
    define("SERVER_IP_ADDRESS", "168.223.1.35");

    /*
        A path using the server's IP address instead of domain name is required in order for images to be exported with PDFs
    */
    define("APP_PATH_IP_ADDRESS", "http://" . SERVER_IP_ADDRESS . "/bootstrap/apps/sacs/");

    $VALID_UPLOAD_EXTENSIONS = array("pdf", "doc", "docx", "xls", "xlsx", "csv", "ppt", "pptx", "pub", "jpg", "jpeg", "png", "gif");

    if (APP_DEV_MODE == true) {
    	define("TABLE_SECTION", "sacs.dev_section");
        define("TABLE_STANDARD_REQUIREMENT", "sacs.dev_standard_requirement");
        define("TABLE_APPENDIX_LINK", "sacs.dev_appendix_link");
        define("TABLE_FILE_UPLOAD", "sacs.dev_file_upload");
        define("TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD", "sacs.dev_appendix_link_has_file_upload");

    } else {
        define("TABLE_SECTION", "sacs.section");
        define("TABLE_STANDARD_REQUIREMENT", "sacs.standard_requirement");
        define("TABLE_APPENDIX_LINK", "sacs.appendix_link");
        define("TABLE_FILE_UPLOAD", "sacs.file_upload");
        define("TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD", "sacs.appendix_link_has_file_upload");
    }
?>

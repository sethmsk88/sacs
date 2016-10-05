<?php
	define("APP_DEV_MODE", true);
	define("APP_NAME", "SACS Accreditation");
	define("APP_PATH",  $_SERVER['DOCUMENT_ROOT'] . "/bootstrap/apps/sacs/");
    define("APP_PATH_URL", "http://" . $_SERVER['HTTP_HOST'] . "/bootstrap/apps/sacs/");
    define("APP_HOMEPAGE", "view1");

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

<?php
	require_once("../includes/globals.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php');
	require_once("../includes/exportPortable_functions.php");

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

	// Create SR header
	$srHeader = "";
	if ($srType == 'r')
		$srHeader = "C.R. ";
	else if ($srType == 's')
		$srHeader = "C.S. ";
	$srHeader .= $srNum;

	$sel_files = "
		SELECT file_upload_id, fileName
		FROM ". TABLE_FILE_UPLOAD ."
	";
	$stmt = $conn->prepare($sel_files);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($fileID, $filename);

	// store query results in array 
	$file_array = array();
	while ($stmt->fetch()) {
		$file_array[$fileID] = $filename;
	}
?>

<div class="container">
	<h4><?= $srHeader ?></h4>

	<div class="row">
		<div class="col-lg-12">
			<h4>Table of Contents</h4>

			<ol class="begin">
				<?php
					// Print sections and their subsections
					$rootID = -1;
					printTOCSection($rootID, $_GET['id'], $conn);
				?>
			</ol>
			<a href="./appendix_<?= $_GET['id'] ?>.html">Appendix</a>
		</div>
	</div>

	<!-- Divider -->
	<div class="row">
		<div class="divider"></div>
	</div>

	<?php
		printBodySection($rootID, $_GET['id'], false, $conn);
	?>
</div>

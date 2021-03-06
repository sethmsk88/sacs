<?php
	// Start Output Buffering
	ob_start();

	require_once "../includes/globals.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/api/mpdf60/mpdf.php';
	require_once "../includes/subNarrative_functions.php";

	// Return the class name for the compliance choice
	function getComplianceClass($val, $choice)
	{
		$selectedChoiceClass = "glyphicon glyphicon-remove";
		if ($val === $choice)
			return $selectedChoiceClass;
		else
			return "";
	}

	function getComplianceMark($val, $choice)
	{
		$mark = '<span style="font-weight:bold; color: red; font-size:1.15em;font-family:\'Courier New\';">X</span>';
		if ($val === $choice)
			return $mark;
		else
			return "";
	}

	// Get SR info
	$sel_sr = "
		SELECT number, descr, narrative, summary, sr_type, compliance
		FROM ". TABLE_STANDARD_REQUIREMENT ."
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param('i', $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srNum, $descr, $narrative, $summary, $srType, $compliance);
	$stmt->fetch();

	// Get supplemental sections
	$sel_supp_sections = "
		SELECT id, name, body, parent_id
		FROM ". TABLE_SECTION ."
		WHERE srid = ?
	";
	$stmt = $conn->prepare($sel_supp_sections);
	$stmt->bind_param('i', $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($sectionID, $sectionName, $sectionBody, $sectionParentID);

	// Store all section information in an array
	$section_arr = array();
	while ($stmt->fetch()) {

		// create object to hold section info
		$section_obj = (object) array(
			'id' => $sectionID,
			'name' => $sectionName,
			'body' => $sectionBody,
			'parent_id' => $sectionParentID
			);
		array_push($section_arr, $section_obj);
	}

	// Get appendix items
	$sel_appendixLinks = "
		SELECT l.appendix_link_id, l.linkName, l.linkURL, l.refNum, f.file_upload_id, f.fileName
		FROM ". TABLE_APPENDIX_LINK ." AS l
		LEFT JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON lhf.appendix_link_id = l.appendix_link_id
		LEFT JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON lhf.file_upload_id = f.file_upload_id
		WHERE l.srid = ?
		ORDER BY l.refNum
	";
	$stmt_appendixLinks = $conn->prepare($sel_appendixLinks);
	$stmt_appendixLinks->bind_param("i", $_GET['id']);
	$stmt_appendixLinks->execute();
	$stmt_appendixLinks->store_result();
	$stmt_appendixLinks->bind_result($linkID, $linkName, $linkURL, $refNum, $fileID, $fileName);

	if ($srType === 's')
		$srPrefix = 'C.S. ';
	else
		$srPrefix = 'C.R. ';
?>

<html>
<head>
	<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../css/main.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="/bootstrap/js/bootstrap.min.js"></script>

	<style type="text/css">
		table.appendix {border-collapse:collapse; width:100%;}
		table.appendix tr td {border:1px solid black; padding:4px;}
		.section-header {border-bottom: 1px solid #aaa; padding-bottom:4px; margin-bottom:8px;}
	</style>
</head>
</script></script></head>
<body>
	<h4><?= $srPrefix ?> <?= $srNum ?></h4>
	<?= $descr ?><br>

	<table style="width:75%;">
		<tr>
			<td><?= getComplianceMark(-1, $compliance) ?>&nbsp;Non-Compliance</td>
			<td><?= getComplianceMark(0, $compliance) ?>&nbsp;Partial Compliance</td>
			<td><?= getComplianceMark(1, $compliance) ?>&nbsp;Compliant</td>
		</tr>
	</table>
	<br>

	<div class="section-header h4">
		Narrative
	</div>
	<?= $narrative ?>
	

	<div class="section-header h4">
		<h4>Summary Statement</h4>
	</div>
	<?= $summary ?>

	<?php
		// Return the current buffer
		$narrative_html = ob_get_contents();

		// Clean the buffer
		ob_clean();
	?>

	<h4>Table of Contents</h4>

	<ol class="begin">
	<?php
		// Print sections and their subsections
		$rootID = -1;
		printTOCSection($rootID, $_GET['id'], $conn);
	?>
	</ol>

	<div class="divider"></div>

<?php
	printBodySection($rootID, $_GET['id'], false, $conn);

	// Return the current buffer, then clean the buffer
	$supplemental_html = ob_get_contents();
	ob_clean();
?>
	
	<h5>DOCUMENTATION</h5>

	<h5><?= $srNum ?> Appendix</h5>
	<table class="appendix">
	<?php
		while ($stmt_appendixLinks->fetch()) {
	?>
		<tr>
			<td><?= $refNum ?>.</td>

			<?php
				// If this is a file reference
				if (!is_null($fileID)) {
					$fileRefLink = APP_GET_FILE_PAGE . '?fileid=' . $fileID;
			?>
				<td><a href="<?= $fileRefLink ?>" target="_blank"><?= $fileName ?></a></td>
			<?php
				} else if ($linkURL != "") {
			?>
				<td><a href="<?= $linkURL ?>" target="_blank"><?= $linkName ?></a></td>
			<?php } else { ?>
				<td><?= $linkName ?></td>
			<?php } ?>
		</tr>
	<?php
		}
	?>
	</table>

</body>
</html>
<?php
	$appendix_html = ob_get_clean();

	// TESTING output statements
	// echo $narrative_html;
	// echo $supplemental_html;
	// echo $appendix_html;
 	// exit;

	$fileName = $srPrefix . $srNum . '.pdf';

	$mpdf = new mPDF();
	$mpdf->SetTitle($fileName);
	$mpdf->WriteHTML($narrative_html);
	$mpdf->AddPage();
	$mpdf->WriteHTML($supplemental_html);
	$mpdf->AddPage();
	$mpdf->WriteHTML($appendix_html);
	$mpdf->Output($fileName, 'D');
	exit;
?>

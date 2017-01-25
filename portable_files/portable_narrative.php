<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
	require_once("../includes/exportPortable_functions.php");
?>

<?php
	function getComplianceMark($val, $choice)
	{
		$mark = '<span style="font-weight:bold; color: red; font-size:1.15em;font-family:\'Courier New\';">X</span>';
		if ($val === $choice)
			return $mark;
		else
			return "";
	}

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

	// Get SR info from DB
	$sel_sr = "
		SELECT number, descr, narrative, summary, sr_type, compliance
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srNum, $descr, $narrative, $summary, $sr_type, $compliance);
	$stmt->fetch();

	// create header
	$header = "";
	if ($sr_type == 'r')
		$header .= 'C.R. ';
	else if ($sr_type == 's')
		$header .= 'C.S. ';
	$header .= $srNum;
?>

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h4 id="srHeader"><?= $header ?></h4>
		</div>
	</div>

	<!-- Title/Description -->
	<div id="descr">
		<?= make_links_portable($descr) ?>
	</div>

	<!-- Compliance Status -->
	<table style="width:35%;">
		<tr>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	<br>

	<!-- Narrative -->
	<h4>Narrative</h4>
	<?= make_links_portable($narrative) ?>

	<!-- Summary -->
	<h4>Summary Statement</h4>
	<?= make_links_portable($summary) ?>

	<div class="row" style="margin-top:8px;">
		<div class="col-lg-3">
			<button id="appendix-btn" class="btn btn-primary btn-sm" style="width:100%;">Appendix</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	$("#appendix-btn").click(function() {
		location.href = './appendix_<?= $_GET["id"] ?>.html';
	});
</script>

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
?>

<script src="./js/narrative.js"></script>

<?php
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

	// set compliance selection
	$complianceChoice_arr[-1] = "";
	$complianceChoice_arr[0] = "";
	$complianceChoice_arr[1] = "";
	$complianceChoice_arr[$compliance] = "glyphicon glyphicon-remove";
?>

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h4 id="srHeader"><?= $header ?></h4>
		</div>
	</div>

	<!-- Title/Description -->
	<div id="descr"></div>

	<!-- Compliance Status -->
	<table style="width:35%;">
		<tr>
			<td><span class="<?= $complianceChoice_arr[-1] ?>"></span>&nbsp;Non-Compliance</td>
			<td><span class="<?= $complianceChoice_arr[0] ?>"></span>&nbsp;Partial Compliance</td>
			<td><span class="<?= $complianceChoice_arr[1] ?>"></span>&nbsp;Compliance</td>
		</tr>
	</table>
	<br>

	<h4>Narrative</h4>
	<?= $narrative ?>

	<h4>Summary Statement</h4>
	<?= $summary ?>

	<div class="row" style="margin-top:8px;">
		<div class="col-lg-3">
			<button id="appendix-btn" class="btn btn-primary btn-sm" style="width:100%;">Appendix</button>
		</div>
	</div>
</div>

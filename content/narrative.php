<script src="./js/narrative.js"></script>

<?php
	if (!isset($_GET['id'])) {
		echo '<div class="text-danger">Error: Standard/Requirement ID not found</div>';
		exit;
	}

	// Get SR info from DB
	$sel_sr = "
		SELECT id, number, descr, narrative, summary, sr_type, compliance
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($SRID, $srNum, $descr, $narrative, $summary, $sr_type, $compliance);
	$stmt->fetch();

	// Check to see if any results were returned
	if ($stmt->num_rows == 0) {
		echo '<div class="text-danger">Error: Standard/Requirement does not exist in database</div>';
		exit;
	}

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
		<div class="col-xs-8">
			<h4><?= $header ?></h4>
		</div>
		<div class="col-xs-4" style="text-align:right;">
			<button id="printEntireDoc-btn" class="btn btn-sm btn-primary" data-srid="<?=$_GET['id']?>">Print Entire Document</button>
		</div>
	</div>

	<!-- Title/Description -->
	<?= $descr ?>

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
			<button id="appendix-<?= $SRID ?>" class="btn btn-primary btn-sm" style="width:100%;">Appendix</button>
		</div>
	</div>
</div>

<?php
	// Include on ALL pages
	require_once(APP_PATH . "includes/functions.php");

	// Require Login
	if (!isset($loggedIn)) {
		exit;
	} else {
		require_login($loggedIn);
	}
?>

<link href="./css/admin.css" rel="stylesheet">

<?php
	$sel_all_SR = "
		SELECT id, number, sr_type
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		ORDER BY sr_type, number
	";
	$stmt = $conn->prepare($sel_all_SR);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($SRID, $number, $sr_type);

	// Array to hold SRs
	$sr_array = array();

	// Insert SRs into array
	while ($stmt->fetch()) {
		if ($sr_type == 'r')
			$sr_array[$SRID] = 'C.R. ' . $number;
		else if ($sr_type == 's')
			$sr_array[$SRID] = 'C.S. ' . $number;
	}
?>

<script src="./js/admin.js"></script>

<div class="container">

	<div class="row">
		<div class="col-lg-12" style="text-align:right;">
			<button id="exportApp-btn" class="btn btn-sm btn-primary">Export Portable Application</button>
		</div>
	</div>

	<table id="admin-table" class="table">
		<tr>
			<th></th>
			<th>Narrative</th>
			<th>Supplemental</th>
			<th>Appendix</th>
			<th></th>
		</tr>
		<?php
			foreach ($sr_array as $SRID => $sr) {
		?>
		<tr>
			<td><?= $sr ?></td>
			<td>
				<div class="btn-group">
					<button id="view-narrative-<?= $SRID ?>" class="btn btn-primary">View</button>
					<button id="edit-narrative-<?= $SRID ?>" class="btn btn-warning">Edit</button>
				</div>
			</td>
			<td>
				<div class="btn-group">
					<button id="view-supplemental-<?= $SRID ?>" class="btn btn-primary">View</button>
					<button id="edit-supplemental-<?= $SRID ?>" class="btn btn-warning">Edit</button>
				</div>
			</td>
			<td>
				<div class="btn-group">
					<button id="view-appendix-<?= $SRID ?>" class="btn btn-primary">View</button>
					<button id="edit-appendix-<?= $SRID ?>" class="btn btn-warning">Edit</button>
				</div>
			</td>
			<td>
				<button id="delete-<?= $SRID ?>" class="btn btn-danger">Delete</button>
			</td>
		</tr>
		<?php
			}
		?>
		<tr>	
			<td>
				<button id="newSR-btn" class="btn btn-success" style="width:100%">
					<span class="glyphicon glyphicon-plus"></span> New
				</button>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
</div>

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
		FROM " . $TABLE_STANDARD_REQUIREMENT . "
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
				<button class="btn btn-success" style="width:100%">
					<span class="glyphicon glyphicon-plus"></span> New
				</button>
			</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>






	<!-- Old design below -->
<?php /*
	<form name="addSR-form" id="addSR-form" action="">
		<div class="row">
			<div class="col-lg-4 col-md-5">
				<div class="form-group">
					<label for="newCR">Add a new Core Requirement</label>
					<div class="input-group">
						<input
							type="text"
							name="newCR"
							id="newCR"
							class="form-control"
							placeholder="(example: 3.2.6)">
						<span class="input-group-btn">
							<button type="button" id="newCR-btn" class="btn btn-primary add-btn">Add</button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-lg-offset-2 col-md-5 col-md-offset-1">
				<label for="newCS">Add a new Comprehensive Standard</label>
				<div class="input-group">
					<input
						type="text"
						name="newCS"
						id="newCS"
						class="form-control"
						placeholder="(example: 3.2.6)">
					<span class="input-group-btn">
						<button type="button" id="newCS-btn" class="btn btn-primary add-btn">Add</button>
					</span>
				</div>
			</div>
		</div>
		<input type="hidden" name="SRType" id="SRType" value="">

		<div class="row">
			<div id="ajax_addResponse" class="col-lg-12">
				<!-- Intentionally left blank -->
			</div>
		</div>
	</form>

	<form id="editSR-form">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="form-group">
					<label for="existingCR">Edit an existing Core Requirement</label>
					<select class="form-control edit-sel" id="existingCR">
						<option value="-1" selected="selected">Select...</option>
						<?php
							foreach ($sr_cr as $SRID => $cr) {
								echo '<option value="'. $SRID .'">' . $cr . '</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<div class="form-group">
					<label for="existingCS ">Edit an existing Comprehensive Standard</label>
					<select class="form-control edit-sel" id="existingCS">
						<option value="-1" selected="selected">Select...</option>
						<?php
							foreach ($sr_cs as $SRID => $cs) {
								echo '<option value="' . $SRID . '">' . $cs . '</option>';
							}
						?>
					</select>
				</div>
			</div>
		</div>
	</form>
*/ ?>
</div>

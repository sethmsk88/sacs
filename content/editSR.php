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

<script src="./js/editSR.js"></script>
<link href="./css/editSR.css" rel="stylesheet">

<?php
	// Make sure the SR id GET var exists
	if (!isset($_GET['id'])) {
		echo '<div class="text-danger">Error: Standard/Requirement ID not found</div>';
		exit;
	}

	// Get SR info from DB
	$sel_sr = "
		SELECT id, number, descr, narrative, summary, sr_type, compliance
		FROM sacs.standard_requirement
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
?>

<div class="container">
	<form>
		<div class="form-group">
			<?php
				// Set Type selection
				$CR_checked = "";
				$CS_checked = "";
				if ($sr_type == 'r')
					$CR_checked = 'checked="checked"';
				else if ($sr_type == 's')
					$CS_checked = 'checked="checked"';
			?>

			<div class="row">
				<div class="col-lg-12">
					<label>Type</label>
					<div class="radio">
						<label>
							<input
								type="radio"
								name="SRType"
								value="s"
								<?= $CS_checked ?>>Comprehensive Standard
						</label>
					</div>
					<div class="radio">
						<label>
							<input
								type="radio"
								name="SRType"
								value="r"
								<?= $CR_checked ?>>Core Requirement
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-5 form-group">
				<label for="srNum">Standard/Requirement Number</label>
				<input
					type="text"
					name="srNum"
					id="srNum"
					class="form-control"
					value="<?= $srNum ?>"
					placeholder="(example: 3.2.6)">
			</div>
		</div>

		<?php
			// Set compliance selection
			$compChecked_arr[-1] = ""; // non-compliance
			$compChecked_arr[0] = ""; // partial-compliance
			$compChecked_arr[1] = ""; // compliance
			$compChecked_arr[$compliance] = ' checked="checked"';
		?>

		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<label>Compliance</label>
					<div class="radio">
						<label>
							<input
								type="radio"
								name="compliance"
								value="-1"
								<?= $compChecked_arr[-1] ?>>Non-Compliance
						</label>
					</div>
					<div class="radio">
						<label>
							<input
								type="radio"
								name="compliance"
								value="0"
								<?= $compChecked_arr[0] ?>>Partial-Compliance
						</label>
					</div>
					<div class="radio">
						<label>
							<input
								type="radio"
								name="compliance"
								value="1"
								<?= $compChecked_arr[1] ?>>Compliance
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="descr">Title/Description</label>
				<textarea
					name="descr"
					id="descr"
					class="form-control richtext textarea-md"><?= $descr ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="narrative">Narrative</label>
				<textarea
					name="narrative"
					id="narrative"
					class="form-control richtext textarea-lg"><?= $narrative ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="summary">Summary</label>
				<textarea
					name="summary"
					id="summary"
					class="form-control richtext textarea-md"><?= $summary ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6 form-group">
				<button id="save-btn" class="btn btn-success" style="width:100%;">Save</button>
			</div>

			<div class="col-lg-4 col-md-5 col-sm-6">
				<button id="view-btn" class="btn btn-primary" style="width:100%;">View Formatted Standard/Requirement</button>
			</div>
		</div>

		<input type="hidden" name="SRID" id="SRID" value="<?= $SRID ?>">
	</form>
</div>

<?php
	// Include on ALL pages
	require_once(APP_PATH . "includes/functions.php");
	require_once './includes/insertRef_modal.php'; // insert reference modal

	// Require Login
	if (!isset($loggedIn)) {
		exit;
	} else {
		require_login($loggedIn);
	}
?>

<script src="./js/editNarrative.js"></script>
<link href="./css/editNarrative.css" rel="stylesheet">

<?php
	// Make sure the SR id GET var exists
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
	if (!$stmt = $conn->prepare($sel_sr)) {
		echo 'error - ' . $conn->error . '<br>';
	} else if (!$stmt->bind_param("i", $_GET['id'])) {
		echo 'error - ' . $stmt->error . '<br>';
	} else if (!$stmt->execute()) {
		echo 'error - ' . $stmt->error . '<br>';
	}
	$stmt->store_result();
	$stmt->bind_result($SRID, $srNum, $descr, $narrative, $summary, $sr_type, $compliance);
	$stmt->fetch();

	// Check to see if any results were returned
	if ($stmt->num_rows == 0) {
		echo '<div class="text-danger">Error: Standard/Requirement does not exist in database</div>';
		exit;
	}

	// Get all references for this SR
	$sel_ref = "
		SELECT linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE srid = ?
		ORDER BY refNum ASC
	";
	if (!$stmt2 = $conn->prepare($sel_ref)) {
		echo 'error - ' . $conn->error . '<br>';
	} else if (!$stmt2->bind_param("i", $_GET['id'])) {
		echo 'error - ' . $stmt2->error . '<br>';
	} else if (!$stmt2->execute()) {
		echo 'error - ' . $stmt2->error . '<br>';
	}
	$stmt2->store_result();
	$stmt2->bind_result($linkName, $linkURL, $refNum);
?>


<div class="container">
	<form
		name="editNarrative-form"
		id="editNarrative-form"
		role="form">

		<div class="form-group">
			<?php
				// Set Type selection
				$CR_checked = "";
				$CS_checked = "";
				if ($sr_type == 'r') {
					$CR_checked = 'checked="checked"';
					$SR_prefix = 'C.R.';
				} else if ($sr_type == 's') {
					$CS_checked = 'checked="checked"';
					$SR_prefix = 'C.S.';
				}
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
				<div class="row">
					<div class="col-lg-12">
						<label for="descr">Title/Description</label>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<button
							title="Insert Reference"
							class="tinymce_btn"
							data-toggle="modal"
							data-target="#insertRefModal">Insert Reference
						</button>
						<!-- <button id="insertRef-btn2" class="tinymce_btn">Another Button</button> -->
					</div>
				</div>
				<textarea
					name="descr"
					id="descr"
					class="form-control richtext richtext-sm"><?= $descr ?></textarea>
			</div>

		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<div class="row">
					<div class="col-lg-12">
						<label for="narrative">Narrative</label>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<button
							title="Insert Reference"
							class="tinymce_btn"
							data-toggle="modal"
							data-target="#insertRefModal">Insert Reference
						</button>
					</div>
				</div>
				<textarea
					name="narrative"
					id="narrative"
					class="form-control richtext richtext-lg"><?= $narrative ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<div class="row">
					<div class="col-lg-12">
						<label for="summary">Summary</label>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<button
							title="Insert Reference"
							class="tinymce_btn"
							data-toggle="modal"
							data-target="#insertRefModal">Insert Reference
						</button>
					</div>
				</div>
				<textarea
					name="summary"
					id="summary"
					class="form-control richtext richtext-md"><?= $summary ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6 form-group">
				<button id="save-btn" class="btn btn-success" style="width:100%;">Save</button>
			</div>

			<div class="col-lg-4 col-md-5 col-sm-6">
				<button id="view-btn" class="btn btn-primary" style="width:100%;">View Formatted <?= $SR_prefix ?></button>
			</div>
		</div>

		<input type="hidden" name="SRID" id="SRID" value="<?= $SRID ?>">
	</form>
</div>

<!-- Autosave Toast Message -->
<div class="toastMessage">
	<!-- Filled with JavaScript -->
</div>

<?php /*
<!-------------- MODALS BELOW ---------------->
<!-- Insert Reference Modal -->
<div id="insertRef-modal" class="modalForm" style="width:500px;">
	<div class="modalForm-header">
		Insert Reference
	</div>
	<div class="modalForm-content">
		<form
			name="insertRef-form"
			id="insertRef-form"
			role="form"
			enctype="multipart/form-data">
			
			<div class="row">
				<div class="col-lg-12">
					<div class="radio">
						<label><input type="radio" name="refChoice" id="refChoice-0" value="0">Add an Existing Reference</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="refChoice" id="refChoice-1" value="1">Add a New Reference</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="refChoice" id="refChoice-2" value="2">Insert Link to Supplemental</label>
					</div>
				</div>
			</div>

			<!-- Existing Reference -->
			<div id="refChoice-0-container" style="display:none;">
				<select name="existingRef" id="existingRef" class="form-control">
					<option value=""></option>
					<?php
						while ($stmt2->fetch()) {
					?>
						<option value="<?= $refNum ?>" data-url="<?= $linkURL ?>"><?= $linkName ?></option>
					<?php
						}
					?>
				</select>
			</div>

			<!-- New Reference -->
			<div id="refChoice-1-container" style="display:none;">
				<div class="row">
					<div class="col-lg-12" style="margin-bottom:8px;">
						<em><b>Note:</b>A reference number will automatically be assigned. You may change the reference number from the appendix edit page.</em>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<label>Select Reference Type</label>
						<div class="radio" style="margin-top:0;">
							<label><input type="radio" name="newRefType" id="newRefType-0" value="0">Enter a URL</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="newRefType" id="newRefType-1" value="1">Attach a File</label>
						</div>
					</div>
				</div>
				<div class="row url-ref file-ref" style="display:none;">
					<div class="col-lg-12 form-group">
						<label for="refName">Reference Name</label>
						<input
							type="text"
							name="refName"
							id="refName"
							class="form-control">
					</div>
				</div>
				<div class="row url-ref" style="display:none;">
					<div class="col-lg-12" class="form-group">
						<label for="refURL">Reference URL</label>
						<input
							type="text"
							name="refURL"
							id="refURL"
							class="form-control">
					</div>
				</div>
				<div class="row file-ref" style="display:none;">
					<div class="col-lg-12" class="form-group">
						<label for="fileToUpload">Select a file to upload</label>
						<div class="input-group">
							<span class="input-group-btn">
								<span class="btn btn-primary btn-file">
									Browse <input type="file" name="fileToUpload" id="fileToUpload">
								</span>
							</span>
							<input type="text" class="form-control" readonly="readonly">
						</div>
					</div>
				</div>
			</div>

			<div id="refChoice-2-container" style="display:none;">
				<div class="row">
					<div class="col-lg-12" class="form-group">
						<label for="linkName">What would you like the link name to be?</label>
						<input
							type="text"
							name="linkName"
							id="linkName"
							class="form-control">
					</div>
				</div>
			</div>

			<input type="hidden" name="srid" value="<?= $SRID ?>">
			<input type="hidden" name="textarea_id" id="textarea_id" value="">

			<div id="submitRef-btn" class="row" style="display:none; margin-top:16px;">
				<div class="col-lg-12">
					<input type="submit" class="btn btn-primary" value="Submit">
				</div>
			</div>
		</form>
		<div id="ajax_insertRefResponse">
			<!-- Filled by ajax response -->
		</div>
	</div>
</div>
*/ ?>

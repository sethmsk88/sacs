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

<script src="./js/editSectionBody.js"></script>

<?php
	// Get section body from DB table
	$sel_section = "
		SELECT srid, body
		FROM " . TABLE_SECTION . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_section);
	$stmt->bind_param("i", $_GET['sid']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srid, $sectionBody);
	$stmt->fetch();

	// Get all references for this SR
	$sel_ref = "
		SELECT linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE srid = ?
		ORDER BY refNum ASC
	";
	$stmt2 = $conn->prepare($sel_ref);
	$stmt2->bind_param("i", $srid);
	$stmt2->execute();
	$stmt2->store_result();
	$stmt2->bind_result($linkName, $linkURL, $refNum);
?>

<div class="container">
	<form id="editSectionBody-form" action="./content/act_subNarrative.php" method="post">
		<input type="hidden" name="sid" value="<?= $_GET['sid'] ?>">

		<div class="row">
			<div class="col-lg-12 form-group">
				<div class="row">
					<div class="col-lg-12">
						<label for="sectionBody">Section Body</label>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<button id="insertRef-btn" class="tinymce_btn">Insert Reference</button>
					</div>
				</div>
				<textarea
					name="sectionBody"
					id="sectionBody"
					class="form-control richtext richtext-lg"><?= $sectionBody ?></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>
		</div>
	</form>
</div>

<!-- Autosave Toast Message -->
<div class="toastMessage">
	<!-- Filled with JavaScript -->
</div>

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
			role="form">
			
			<div class="row">
				<div class="col-lg-12">
					<div class="radio">
						<label><input type="radio" name="refChoice" id="refChoice-0" value="0">Add an Existing Reference</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="refChoice" id="refChoice-1" value="1">Add a New Reference</label>
					</div>
				</div>
			</div>

			<!-- Existing Reference -->
			<div id="refChoice-0-container" style="display:none;">
				<div class="row">
					<div class="col-lg-3">
						<select name="existingRefNum" id="existingRefNum" class="form-control">
							<option value="">#</option>
							<?php
								while ($stmt2->fetch()) {
							?>
								<option value="<?= $refNum ?>"><?= $refNum ?></option>
							<?php
								}
								$stmt2->data_seek(0); // rewind stmt2 iterator
							?>
						</select>
					</div>
					<div class="col-lg-9">
						<select name="existingRef" id="existingRef" class="form-control">
							<option value="">Reference</option>
							<?php
								while ($stmt2->fetch()) {
							?>
								<option value="<?= $refNum ?>" data-url="<?= $linkURL ?>"><?= $linkName ?></option>
							<?php
								}
							?>
						</select>
					</div>
				</div>
			</div>

			<!-- New Reference -->
			<div id="refChoice-1-container" style="display:none;">
				<div class="row">
					<div class="col-lg-12" style="margin-bottom:8px;">
						Note: A reference number will automatically be assigned. You may change the reference number from the appendix edit page.
					</div>
					<div class="col-lg-12 form-group">
						<label for="refName">Reference Name</label>
						<input
							type="text"
							name="refName"
							id="refName"
							class="form-control">
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12" class="form-group">
						<label for="refURL">Reference URL</label>
						<input
							type="text"
							name="refURL"
							id="refURL"
							class="form-control">
					</div>
				</div>
			</div>

			<input type="hidden" name="srid" value="<?= $srid ?>">
			<input type="hidden" name="textarea_id" id="textarea_id" value="">

			<div id="submitRef-btn" class="row" style="display:none; margin-top:12px;">
				<div class="col-lg-12">
					<input type="submit" class="btn btn-primary" value="Submit">
				</div>
			</div>


		</form>
	</div>
</div>

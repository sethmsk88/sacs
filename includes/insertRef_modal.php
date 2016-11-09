<?php
	// Get all references for this SR
	$sel_ref = "
		SELECT linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE srid = ?
		ORDER BY refNum ASC
	";
	$stmt = $conn->prepare($sel_ref);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkName, $linkURL, $refNum);
?>

<div
	class="modal fade"
	id="insertRefModal"
	role="dialog"
	aria-labelledby=""
	aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button
					type="button"
					class="close"
					data-dismiss="modal"
					aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Insert Reference</h4>
			</div>
			<div class="modal-body">
				<form
					name="insertRef-form"
					id="insertRef-form"
					role="form"
					enctype="multipart/form-data">

					<!-- Choose a reference type -->
					<div id="box-1">
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
					</div>
					
					<!-- Add an existing reference -->
					<div id="box-2" style="display:none;">
						<div class="row">
							<div class="col-lg-12">
								<label for="existingRef">Select a reference</label>
								<select name="existingRef" id="existingRef" class="form-control">
									<option value=""></option>
									<?php
										while ($stmt->fetch()) {
									?>
										<option value="<?= $refNum ?>" data-url="<?= $linkURL ?>"><?= $linkName ?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
					</div>

					<!-- Add a new reference -->
					<div id="box-3" style="display:none;">
						<div class="row">
							<div class="col-lg-12" style="margin-bottom:8px;">
								<em><b>Note:</b>A reference number will automatically be assigned. You may change the reference number from the appendix edit page.</em>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12 form-group">
								<label for="refName">Reference Name</label>
								<input
									type="text"
									name="refName"
									id="refName"
									class="form-control">
							</div>
						</div>
						<div id="box-3-1">
							<div class="row">
								<div class="col-lg-12 form-group">
									<label for="refURL">Reference URL</label>
									<input
										type="text"
										name="refURL"
										id="refURL"
										class="form-control">
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12" class="form-group">
									<button id="attachFile-btn" class="btn btn-primary">Attach File</button>
								</div>
							</div>
						</div>

						<div id="box-3-2" class="row" style="margin-top:12px; display:none;">
							<div class="col-lg-12 form-group">
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

					<div id="box-4" style="display:none;">
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


<!--
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
						-->


					<!-- <div class="row">
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
					
					<div class="row" style="margin-top:12px;">
						<div class="col-lg-12" class="form-group">
							<button id="attachFile-btn" class="btn btn-primary">Attach File</button>
						</div>
					</div>
					
					<div id="box-3" style="display:none;">
						<div class="row">
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
					</div> -->

					<input type="hidden" name="srid" value="<?= $_GET['id'] ?>">
					<input type="hidden" name="textarea_id" id="textarea_id" value="">
					<input type="hidden" name="_refChoice" id="_refChoice" value="">
				</form>

				<div class="row">
					<div id="ajax_response" class="col-lg-12" style="margin-top:8px;">
						<!-- To Be Filled with AJAX response messages --> 
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button
					type="button"
					class="btn btn-default"
					data-dismiss="modal">
					Cancel
				</button>
				<button
					type="button"
					class="btn btn-primary"
					id="insertRefSubmit">
					Submit
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var resetModalFields = function() {
		$('#insertRefModal #box-1').show();
		$('#insertRefModal #box-2').hide();
		$('#insertRefModal #box-3').hide();
		$('#insertRefModal #box-4').hide();

		$('#insertRefModal input[type="text"]').val("");
		$('#insertRefModal input[type="radio"]').prop('checked', false);
		$('#insertRefModal select option:eq(0)').prop('selected', true);
	}

	var swapFade = function(fadeOut_sel, fadeIn_sel) {
		$('#insertRefModal ' + fadeOut_sel).fadeOut(function() {
			$('#insertRefModal ' + fadeIn_sel).fadeIn();
		});
	}

	// Reference Choice Handler
	$('#insertRefModal input[name="refChoice"]').change(function() {
		var refChoice = $(this).val();

		// Set hidden input value
		$('#insertRef-form #_refChoice').val(refChoice);

		if (refChoice == 0) {
			swapFade('#box-1', '#box-2');
		} else if (refChoice == 1) {
			swapFade('#box-1', '#box-3')
		} else if (refChoice == 2) {
			swapFade('#box-1', '#box-4')
		}
	});

	// Handler for when modal is closed/cancelled
	$('#insertRefModal').on('hidden.bs.modal', function(e) {
		var refChoice = $('#insertRef-form input[name="refChoice"]').val(); // DEBUGGING

		resetModalFields();
	});

	// Handler for when attach file button is clicked in the modal
	$('#insertRefModal #attachFile-btn').click(function(e) {
		e.preventDefault();

		$('#insertRefModal #refURL').val(''); // clear refURL field
		swapFade('#box-3-1', '#box-3-2');
	});

	// Form submit handler
	$('#insertRefModal').find('.modal-footer #insertRefSubmit').on('click', function() {

		var refChoice = $('#insertRef-form input[name="_refChoice"]').val();

		// Add an Existing Reference
		if (refChoice === "0") {

			// pull info from inputs and use it to create a ref link
			$existingRef = $('#insertRef-form #existingRef').children(':selected');
			var refURL = $existingRef.attr('data-url');
			var refNum = $existingRef.val();
			var refLink = '<a href="' + refURL + '" target="_blank">[' + refNum + ']</a>';

			$richTextArea = $('#' + $('#textarea_id').val()).closest('div.form-group').find('iframe');

			// Append ref link to richtextarea
			$richTextArea.tinymce_append(refLink);

			// Hide the modal
			$('#insertRefModal').modal('hide');

		} else if (refChoice === "1") {
		// Add a new reference

			// Enforce Required Fields
			var errors = new Array();
			if ($('#insertRefModal #refName').val() === "") {
				errors.push('Reference Name is required');
			}

			// Require file to be attached if file input is visible
			if ($('#insertRefModal #fileToUpload').is(':visible') === true) {
				if ($('#insertRefModal #fileToUpload').get(0).files.length === 0) {
					errors.push('Please select a file to upload');
				}
			}

			// Display Errors
			if (errors.length > 0) {
				$('#insertRefModal #ajax_response').html(displayErrors(errors));
				return; // halt form submission
			}

			// Set insertRefType field
			// This field is used in the action file to determine whether or not we are attaching a file as a reference or simply using a URL as a reference
			if ($('#insertRefModal #fileToUpload').is(':visible') === true) {
				var newRefType = 1; // file reference
			} else {
				var newRefType = 0; // URL reference
			}

			// Create FormData object and populate with all form fields
			var formData = new FormData($('#insertRefModal #insertRef-form')[0]);
			formData.append('insertRefType', newRefType);

			// The following key/value pair is created so this POST is compatible with the action file we are using
			formData.append('refChoice', 1); // required var for the action file

			$.ajax({
				url: './content/act_insertRef.php',
				type: 'POST',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false,
				success: function(response) {

					// If there are errors
					if (response.hasOwnProperty('errors') &&
						response['errors'].length > 0) {

						$('#insertRefModal #ajax_response').html(displayErrors(response['errors']));
					} else {
						location.reload();
					}
				}
			});

		} else if (refChoice === "2") {
		// Insert a link to the supplemental

			var supURL = "?page=subNarrative&id=" + $('#SRID').val();
			var linkName = $('#insertRef-form #linkName').val();

			// create link
			var supLink = '<a href="' + supURL + '">' + linkName + '</a>';

			$richTextArea = $('#' + $('#textarea_id').val()).closest('div.form-group').find('iframe');

			// Append ref link to richtextarea
			$richTextArea.tinymce_append(supLink);

			// Hide the modal
			$('#insertRefModal').modal('hide');
		}
	});

</script>

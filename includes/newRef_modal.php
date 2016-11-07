<div
	class="modal fade"
	id="newRefModal"
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
				<h4 class="modal-title">Add New Reference</h4>
			</div>
			<div class="modal-body">
				<form
					name="newRef-form"
					id="newRef-form"
					role="form"
					enctype="multipart/form-data">

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

					<div id="box-1">
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
					</div>

					<!-- Get srid from URL -->
					<input type="hidden" name="srid" value="<?= $_GET['id'] ?>">
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
					id="newRefSubmit">
					Save Changes
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	function displayErrors(error_arr) {
		var openErrTag = '<div class="text-danger">';
		var closeErrTag = '</div>';
		var errMsg = openErrTag + "<b>An Error Has Occurred!</b>" + closeErrTag;

		for (var i=0; i < error_arr.length; i++) {
			errMsg += openErrTag + error_arr[i] + closeErrTag;
		}

		return errMsg;
	}

	// Dialog show event handler
	$('#newRefModal').on('show.bs.modal', function(e) {
		// TODO: reset modal input fields
	});

	// Dialog hide/cancel event handler
	$('#newRefModal').on('hide.bs.modal', function(e) {
		$('#newRefModal input').val("");
		$('#newRefModal #box-1').show();
		$('#newRefModal #box-3').hide();
	});

	// Submit form when ENTER key is pressed while focused on a textbox
	$('#newRefModal input[type="text"]').keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			$('#newRefSubmit').click();
		}
	});

	// Form submit handler
	$('#newRefModal').find('.modal-footer #newRefSubmit').on('click', function() {

		// Check Required Fields
		var errors = new Array();
		if ($('#newRefModal #refName').val() === "") {
			errors.push('Reference Name is required');
		}

		// If file selector is visible, a file is required to be attached
		if ($('#newRefModal #fileToUpload').is(':visible') === true) {
			if ($('#newRefModal #fileToUpload').get(0).files.length === 0) {
				errors.push('Please select a file to upload');
			}
		}

		// If there are any errors, display them and stop the form submission
		if (errors.length > 0) {
			$('#newRefModal #ajax_response').html(displayErrors(errors));
			return; // halt form submission
		}

		// Set newRefType field
		// This field is used in the action file to determine whether or not we are attaching a file as a reference or simply using a URL as a reference
		if ($('#newRefModal #fileToUpload').is(':visible') === true) {
			var newRefType = 1; // file reference
		} else {
			var newRefType = 0; // URL reference
		}

		// Create FormData object and populate with all form fields
		var formData = new FormData($('#newRefModal #newRef-form')[0]);
		formData.append('newRefType', newRefType);

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

					$('#newRefModal #ajax_response').html(displayErrors(response['errors']));
				} else {
					location.reload();
				}
			}
		});
	});

	// Attach File button click handler
	$('#newRefModal #attachFile-btn').click(function(e) {
		e.preventDefault();

		$('#newRefModal #refURL').val(''); // clear refURL field

		$('#newRefModal #box-1').fadeOut(function() {
			$('#newRefModal #box-3').fadeIn();
		});
	});
</script>

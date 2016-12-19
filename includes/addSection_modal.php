<?php
	$modalName = "addSection";
?>

<div
	class="modal fade"
	id="<?=$modalName?>-modal"
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
				<h4 class="modal-title">Add New Section</h4>
			</div>
			<div class="modal-body">
				<form
					name="<?=$modalName?>-form"
					id="<?=$modalName?>-form"
					role="form">

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="sectionName">Section Name</label>
							<input
								type="text"
								name="sectionName"
								id="sectionName"
								class="form-control">
						</div>
					</div>

					<!-- Get srid from URL -->
					<input type="hidden" name="srid" id="srid" value="<?= $_GET['id'] ?>">

					<!-- Action being performed on action page -->
					<input type="hidden" name="action" value="0">
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
					id="<?=$modalName?>-submit">
					Submit
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
	$('#<?=$modalName?>-modal').on('show.bs.modal', function(e) {
		// Not yet implemented
	});

	// Dialog hide/cancel event handler
	$('#<?=$modalName?>-modal').on('hide.bs.modal', function(e) {
		// TODO: reset modal input fields
	});

	// Submit form when ENTER key is pressed while focused on a textbox
	$('#<?=$modalName?>-modal input[type="text"]').keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			$('#<?=$modalName?>-submit').click();
		}
	});

	// Form submit handler
	$('#<?=$modalName?>-modal').find('.modal-footer #<?=$modalName?>-submit').on('click', function() {

		// Check Required Fields
		var errors = new Array();
		if ($('#<?=$modalName?>-modal #sectionName').val() === "") {
			errors.push('Section Name is required');
		}

		if ($('#<?=$modalName?>-modal #srid').val() == "") {
			errors.push('SRID is missing');
		}

		// If there are any errors, display them and stop the form submission
		if (errors.length > 0) {
			$('#<?=$modalName?>-modal #ajax_response').html(displayErrors(errors));
			return; // halt form submission
		}

		$.ajax({
			url: './content/act_supplemental.php',
			type: 'POST',
			data: $('#<?=$modalName?>-form').serialize(),
			dataType: 'json',
			success: function(response) {

				// If there are errors
				if (response.hasOwnProperty('errors') &&
					response['errors'].length > 0) {

					$('#<?=$modalName?>-modal #ajax_response').html(displayErrors(response['errors']));
				} else {
					location.reload();
				}
			}
		});
	});
</script>

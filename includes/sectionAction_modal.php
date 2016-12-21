<?php
	$modalName = "sectionAction";
?>

<div
	class="modal fade"
	id="<?= $modalName ?>-modal"
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
					name="<?= $modalName ?>-form"
					id="<?= $modalName ?>-form"
					role="form">

					<div class="row actionElement action-1 hidden">
						<div id="parentSectionName" class="col-lg-12" style="margin-bottom: 8px;"></div>
					</div>

					<div class="row actionElement action-0 action-1 hidden">
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
					<input type="hidden" name="srid" id="srid">

					<!-- Action being performed on action page -->
					<input type="hidden" name="action" id="action">

					<!-- section id of parent section -->
					<input type="hidden" name="sectionid" id="sectionid">
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
					id="<?= $modalName ?>-submit">
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
	$('#<?= $modalName ?>-modal').on('show.bs.modal', function(e) {

		// Get action value
		var modalAction = $(e.relatedTarget).attr('data-action');

		// Get modal title
		var modalTitle = $(e.relatedTarget).attr('title');
		$('#<?= $modalName ?>-modal .modal-title').text(modalTitle);

		// Get parent section name
		var parentSectionName = $(e.relatedTarget).attr('data-sectionname');
		if (typeof(parentSectionName) !== 'undefined') {
			var parentSectionHTML = '<strong>Note:</strong> This section will be nested within the <em><strong>' + parentSectionName + '</strong></em> section.'
			$('#parentSectionName').html(parentSectionHTML);
		}

		// Fill hidden input fields with values in HTML "data" attributes
		$('#<?= $modalName ?>-modal #action').val(modalAction);
		$('#<?= $modalName ?>-modal #srid').val("<?=$_GET['id']?>");
		var sectionid = $(e.relatedTarget).attr('data-sectionid');
		$('#<?= $modalName ?>-modal #sectionid').val(sectionid);

		

		// Show appropriate elements for this action
		var actionClass = 'action-' + modalAction;
		$('#<?= $modalName ?>-modal .' + actionClass).removeClass('hidden');
	});

	// Dialog hide/cancel event handler
	$('#<?= $modalName ?>-modal').on('hide.bs.modal', function(e) {
		
		// Reset modal input fields
		$('#<?= $modalName ?>-modal input[type="text"]').val('');
		$('#<?= $modalName ?>-modal input[type="hidden"]').val('');

		// Hide all action related elements
		$('#<?= $modalName ?>-modal .actionElement').addClass('hidden');
	});

	// Submit form when ENTER key is pressed while focused on a textbox
	$('#<?= $modalName ?>-modal input[type="text"]').keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			$('#<?= $modalName ?>-submit').click();
		}
	});

	// Form submit handler
	$('#<?= $modalName ?>-modal').find('.modal-footer #<?= $modalName ?>-submit').on('click', function() {

		// Check Required Fields
		var errors = new Array();
		if ($('#<?= $modalName ?>-modal #sectionName').val() === "") {
			errors.push('Section Name is required');
		}

		if ($('#<?= $modalName ?>-modal #srid').val() == "") {
			errors.push('SRID is missing');
		}

		// If there are any errors, display them, else submit the form
		if (errors.length > 0) {
			$('#<?= $modalName ?>-modal #ajax_response').html(displayErrors(errors));
		} else {

			$.ajax({
				url: './content/act_supplemental.php',
				type: 'POST',
				data: $('#<?= $modalName ?>-form').serialize(),
				dataType: 'json',
				success: function(response) {

					// If there are errors
					if (response.hasOwnProperty('errors') &&
						response['errors'].length > 0) {

						$('#<?= $modalName ?>-modal #ajax_response').html(displayErrors(response['errors']));
					} else {
						location.reload();
					}
				}
			});
		}
	});
</script>

$(document).ready(function() {

	/*********************************************/
	/* Methods to handle Insert Reference button */
	hideOverlay = function() {
		$('#overlay').fadeOut();
	}

	hideModalForms = function() {
		$('.modalForm:visible').each(function() {
			$(this).slideUp(hideOverlay);
		});
	}

	resetInputFields = function(formID) {
		if (formID == "insertRef-form") {
			$('#' + formID + ' input[name="refChoice"]').removeAttr('checked');
			$('#' + formID + ' input[type="text"]').val('');
			$('#' + formID + ' [id="textarea_id"]').val('');

			// set select box to first selection
			$('#' + formID + ' #existingRef').val('');

			// hide appropriate sections
			$('#' + formID + ' #refChoice-0-container').hide();
			$('#' + formID + ' #refChoice-1-container').hide();
			$('#' + formID + ' #refChoice-2-container').hide();
		}
	}

	// Insert Reference button click handler
	$('.tinymce_btn').click(function(e) {
		e.preventDefault();
	
		// Reset insertRef-form inputs
		resetInputFields('insertRef-form');

		// get id of related textarea
		var textarea_id = $(this).closest('div.form-group').find('textarea').attr('id');

		// insert textarea_id into modal form
		$('#textarea_id').val(textarea_id);

		// Show insert reference modal, and pass id of textarea as param
		showModal('insertRef-modal');
	});

	// Insert Reference Form Submission Handler
	$('#insertRef-form').submit(function(e) {
		e.preventDefault();

		$form = $(this);

		var id_parts = $('input[name="refChoice"]:checked').attr('id').split('-');
		var refChoice = id_parts[1];

		// Adding and Existing Reference
		if (refChoice === "0") {

			// pull info from inputs and use it to create a ref link
			$existingRef = $('#existingRef').children(':selected');
			var refURL = $existingRef.attr('data-url');
			var refNum = $existingRef.val();
			var refLink = '<a href="' + refURL + '" target="_blank">[' + refNum + ']</a>';

			$richTextArea = $('#' + $('#textarea_id').val()).closest('div.form-group').find('iframe');

			// Append ref link to richtextarea
			$richTextArea.tinymce_append(refLink);

		// Adding a New Reference
		} else if (refChoice === "1") {

			$.ajax({
				type: 'post',
				url: './content/act_insertRef.php',
				data: $form.serialize(),
				dataType: 'json',
				success: function(response) {
					/*
						NOTE: for the selector below to work, the richtextarea must be within the same form-group div as the "Insert Reference" button
					*/
					// Select the richTextArea iframe
					$richTextArea = $('#' + response['textarea_id']).closest('div.form-group').find('iframe');

					// Create ref link
					var refLink = '<a href="' + response['refURL'] + '" target="_blank">[' + response['refNum'] + ']</a>';

					// Append ref link to richtextarea
					$richTextArea.tinymce_append(refLink);
				}
			});
		}

		// Clear fields in modal
		resetInputFields('insertRef-form');

		hideModalForms();
	});

	// Reference choice change handler
	$('input[name="refChoice"]').change(function() {
		var refChoice = $(this).val();

		// Show the appropriate input fields
		if (refChoice === "0") {
			$('#refChoice-1-container').hide();
			$('#refChoice-0-container').slideDown();
		} else if (refChoice === "1") {
			$('#refChoice-0-container').hide();
			$('#refChoice-1-container').slideDown();
		}

		// if submit button is hidde, show it
		if (!$('#submitRef-btn').is(':visible')) {
			$('#submitRef-btn').show();
		}
	});
});

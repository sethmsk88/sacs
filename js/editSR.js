$(document).ready(function () {

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
		}
	}

	// Save button event handler
	$('#save-btn').click(function(e) {
		e.preventDefault();

		$btn = $(e.target); // convert clicked button to jQuery obj

		// Get parent form of clicked button
		var form = $btn.closest('form');

		// Save contents of tinymce rich textareas
		// This is required for richtext to be posted
		tinymce.triggerSave();

		$.ajax({
			url: './content/act_editSR.php',
			type: 'post',
			data: $(form).serialize(),
			success: function(response) {
				alert("Changes have been saved!");
			}
		});
	});

	// View button event handler
	$('#view-btn').click(function(e) {
		e.preventDefault();

		// get SRID from hidden input field
		var SRID = $('#SRID').val();

		location.href = "?page=view2&id=" + SRID;
	});

	// When SR type changes, change the text on the "View" button
	$("input[name='SRType']").change(function() {
		$btn = $('#view-btn');
		var btn_text = "View Formatted ";

		if ($(this).val() == 'r')
			$btn.text(btn_text + 'C.R.');
		else if ($(this).val() == 's')
			$btn.text(btn_text + 'C.S.');

	});

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

		if (refChoice === "0") {
			// pull info from inputs and use it to create a ref link
			$existingRef = $('#existingRef').children(':selected');
			var refURL = $existingRef.attr('data-url');
			var refNum = $existingRef.val();
			var refLink = '<a href="' + refURL + '" target="_blank">[' + refNum + ']</a>';

			$richTextArea = $('#' + $('#textarea_id').val()).closest('div.form-group').find('iframe');

			// Append ref link to richtextarea
			$richTextArea.tinymce_append(refLink);
		} else {

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
		} else {
			$('#refChoice-0-container').hide();
			$('#refChoice-1-container').slideDown();
		}

		// if submit button is hidde, show it
		if (!$('#submitRef-btn').is(':visible')) {
			$('#submitRef-btn').show();
		}
	});
	
});

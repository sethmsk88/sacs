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
			$('#' + formID + ' #refChoice-2-container').hide();
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
			url: './content/act_editNarrative.php',
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

		location.href = "?page=narrative&id=" + SRID;
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

		// Inserting Link to Supplemental
		} else if (refChoice === "2") {

			var supURL = "?page=subNarrative&id=" + $('#SRID').val();
			var linkName = $('#linkName').val();
			
			// create link
			var supLink = '<a href="' + supURL + '">' + linkName + '</a>';

			$richTextArea = $('#' + $('#textarea_id').val()).closest('div.form-group').find('iframe');

			// Append ref link to richtextarea
			$richTextArea.tinymce_append(supLink);
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
			$('#refChoice-2-container').hide();
			$('#refChoice-0-container').slideDown();
		} else if (refChoice === "1") {
			$('#refChoice-0-container').hide();
			$('#refChoice-2-container').hide();
			$('#refChoice-1-container').slideDown();
		} else if (refChoice === "2") {
			$('#refChoice-0-container').hide();
			$('#refChoice-1-container').hide();
			$('#refChoice-2-container').slideDown();
		}

		// if submit button is hidde, show it
		if (!$('#submitRef-btn').is(':visible')) {
			$('#submitRef-btn').show();
		}
	});

	/*$('#save-btn').click(function(e) {
		e.preventDefault();

		$btn = $(e.target); // convert clicked button to jQuery obj

		// Get parent form of clicked button
		var form = $btn.closest('form');

		// Save contents of tinymce rich textareas
		// This is required for richtext to be posted
		tinymce.triggerSave();

		$.ajax({
			url: './content/act_editNarrative.php',
			type: 'post',
			data: $(form).serialize(),
			success: function(response) {
				alert("Changes have been saved!");
			}
		});
	});*/
});

var autoSaveForm = function() {
	// Save contents of tinymce rich textareas
	// This is required for richtext to be posted
	tinymce.triggerSave();

	$.ajax({
		url: './content/act_editNarrative.php',
		type: 'post',
		data: $('#editNarrative-form').serialize(),
		success: function(response) {
			
		}
	});
};

var applyRichTextAreaEventHandlers = function() {
	$('.richtext').each( function() {
		var textArea_id = $(this).attr('id');
		$richTextArea_iframe = $('#' + textArea_id + '_ifr');
		$richTextArea_body = $richTextArea_iframe.contents().find('body');

		// Autosave the form after a change is made within a richtextarea, and there has been 3 seconds of inactivity.
		var timeout_id;
		$richTextArea_body.on('input propertychange change paste', function() {
			console.log('change detected');

			clearTimeout(timeout_id);
			timeout_id = setTimeout(function() {
				autoSaveForm();
			}, 3000);
		});
	});
}

// Apply event handlers to richtextareas after a slight delay
// Waiting on tinymce initialization
setTimeout(applyRichTextAreaEventHandlers, 2000);

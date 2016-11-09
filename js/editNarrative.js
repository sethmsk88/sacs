$(document).ready(function () {

	hideOverlay = function() {
		$('#overlay').fadeOut();
	}

	hideModalForms = function() {
		$('.modalForm:visible').each(function() {
			$(this).slideUp(hideOverlay);
		});
	}

	resetInputFields = function(containerID) {
		if (containerID == "insertRef-form") {
			$('#' + containerID + ' input[name="refChoice"]').removeAttr('checked');
			$('#' + containerID + ' input[name="newRefType"]').removeAttr('checked');
			$('#' + containerID + ' input[type="text"]').val('');
			$('#' + containerID + ' [id="textarea_id"]').val('');

			// set select box to first selection
			$('#' + containerID + ' #existingRef').val('');

			// hide appropriate sections
			$('#' + containerID + ' #refChoice-0-container').hide();
			$('#' + containerID + ' #refChoice-1-container').hide();
			$('#' + containerID + ' #refChoice-2-container').hide();

		} else if (containerID == "refChoice-1-container") {
			$('#' + containerID + ' input[type="radio"]').removeAttr('checked');
			$('#' + containerID + ' input[type="text"]').val("");
			$('#' + containerID + ' input[type="file"]').val("");
			$('#' + containerID + ' .url-ref').hide();
			$('#' + containerID + ' .file-ref').hide();
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


	// New tinymce button click handler
	$('.tinymce_btn').click(function(e) {
		e.preventDefault();

		// get id of related textarea
		var textarea_id = $(this).closest('div.form-group').find('textarea').attr('id');

		// Focus on tinymce editor so that the tinymce_insertAtCaret function will work
		tinymce.get(textarea_id).focus();

		// insert textarea_id into modal form
		$('#insertRef-form #textarea_id').val(textarea_id);		
	});
});

// Show the toast message
var showToastMessage = function(msg) {
	$toast = $('.toastMessage');
	$toast.html(msg);

	// If toast is hidden, show it
	if ($toast.css('display') == 'none')
		$toast.slideDown();
}

var autoSaveForm = function() {
	// Save contents of tinymce rich textareas
	// This is required for richtext to be posted
	tinymce.triggerSave();

	$.ajax({
		url: './content/act_editNarrative.php',
		type: 'post',
		data: $('#editNarrative-form').serialize(),
		success: function(response) {
			showToastMessage('<span class="text-success">All Changes Saved</span>')
		}
	});
};

var saveAfterDelay = function(timeout_id, delay) {
	// show toast message
	showToastMessage('Saving...');

	clearTimeout(timeout_id);
	timeout_id = setTimeout(function() {
		autoSaveForm();
	}, delay);
}

var applyEventHandlers = function() {
	var delay = 3000; // milliseconds

	$('.richtext').each( function() {
		var textArea_id = $(this).attr('id');
		$richTextArea_iframe = $('#' + textArea_id + '_ifr');
		$richTextArea_body = $richTextArea_iframe.contents().find('body');

		// Autosave the form after a change is made within a richtextarea, and there has been 3 seconds of inactivity.
		var timeout_id;
		$richTextArea_body.on('input propertychange change paste', function() {
			saveAfterDelay(timeout_id, delay);
		});
	});

	var timeout_id;
	$('#editNarrative-form input[type="radio"]').on('input propertychange change paste', function() {
			saveAfterDelay(timeout_id, delay);
		});
	$('#editNarrative-form input[type="text"]').on('change', function() {
		saveAfterDelay(timeout_id, delay);
	});
}

// Apply event handlers to richtextareas after a slight delay
// Waiting on tinymce initialization
setTimeout(applyEventHandlers, 2000);

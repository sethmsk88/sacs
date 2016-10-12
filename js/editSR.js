$(document).ready(function () {

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
	$('#insertRef-btn').click(function(e) {
		e.preventDefault();
	
		// get id of related textarea
		var textarea_id = $(this).closest('div.row').find('textarea').attr('id');

		// insert textarea_id into modal form
		$('#textarea_id').val(textarea_id);

		// Show insert reference modal, and pass id of textarea as param
		showModal('insertRef-modal');

		/*
		$richTextArea.tinymce_append('<a href="#">[5]</a> I\'m testing this</b>');
		*/
		
		//$('#descr').tinymce_append('<a href="#">[5]</a> I\'m testing this</b>');
	});

	hideOverlay = function() {
		$('#overlay').fadeOut();
	}

	hideModalForms = function() {
		$('.modalForm:visible').each(function() {
			$(this).slideUp(hideOverlay);
		});
	}

	// Insert Reference Form Submission Handler
	$('#insertRef-form').submit(function(e) {
		e.preventDefault();

		$form = $(this);

		$.ajax({
			type: 'post',
			url: './content/act_insertRef.php',
			data: $form.serialize(),
			dataType: 'json',
			success: function(response) {
				
				/*
					NOTE: for the selector below to work, the richtextarea must be within the same row div as the "Insert Reference" button
				*/
				// Select the richTextArea iframe
				$richTextArea = $('#' + response['textarea_id']).closest('div.row').find('iframe');

				// Create ref link
				var refLink = '<a href="' + response['refURL'] + '" target="_blank">[' + response['refNum'] + ']</a>';

				// Append ref link to richtextarea
				$richTextArea.tinymce_append(refLink);

				// Clear fields in modal
				$('input[type="text"]').val('');
				$('input[id="textarea_id"]').val('');

				hideModalForms();
			}
		});
	});
	
});

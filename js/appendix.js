$(document).ready(function() {
	
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
			$('#' + formID + ' input[type="text"]').val('');
		}
	}

	// Reference Type handler
	$('input[name="refType"]').change(function() {
		if ($(this).val() == 0) {
			$('#fileUploadForm-container').hide();
			$('#URLForm-container').show();
		} else if ($(this).val() == 1) {
			$('#URLForm-container').hide();
			$('#fileUploadForm-container').show();
		}
	});

	// Up Arrow Click Handler
	$('button.up-arrow').click(function(e) {
		e.preventDefault();

		// get link ids for this arrow and the arrow above it
		var this_linkID = $(this).closest('tr').next().attr('data-linkid');
		var above_linkID = $(this).closest('tr').prev().prev().attr('data-linkid');

		$.ajax({
			url: './content/act_appendix.php',
			type: 'post',
			data: {
				'actionType': 0, // change order action type
				'linkID_1': this_linkID,
				'linkID_2': above_linkID
			},
			success: function(response) {
				location.reload();
			}
		});
	});

	// Down Arrow Click Handler
	$('button.down-arrow').click(function(e) {
		e.preventDefault();

		// get link ids for this arrow and the arrow above it
		var this_linkID = $(this).closest('tr').prev().attr('data-linkid');
		var below_linkID = $(this).closest('tr').next().next().attr('data-linkid');

		$.ajax({
			url: './content/act_appendix.php',
			type: 'post',
			data: {
				'actionType': 0, // change order action type
				'linkID_1': this_linkID,
				'linkID_2': below_linkID
			},
			success: function(response) {
				location.reload();
			}
		});
	});	

	// Insert New Reference Form Submission Handler
	$('#insertRef-form').submit(function(e) {
		e.preventDefault();

		$form = $(this);

		$.ajax({
			type: 'post',
			url: './content/act_insertRef.php',
			data: $form.serialize(),
			dataType: 'json',
			success: function(response) {
				location.reload();
			}
		});		
	});

	// Handler for Export Appendix to PDF button
	$('#exportAppendix-btn').click(function(e) {
		e.preventDefault();

		var srid = $(this).attr('data-srid');
		window.open("./content/exportToPDF.php?id=" + srid, "_blank");
	});

	// Handler for Export Attachments to ZIP button
	$('#exportAttachments-btn').click(function(e) {
		e.preventDefault();

		var srid = $(this).attr('data-srid');

		// Using AJAX, see if there are any attached files
		$.ajax({
			type: 'POST',
			url: './content/act_existAttachments.php',
			data: {
				'srid': srid
			},
			success: function(response) {
				if (parseInt(response) > 0) {
					// Create ZIP file
					window.open("./content/exportAttachments.php?id=" + srid, "_blank");
				}
				else {
					showAlert('There are no attachments to export.', '<span class="text-info">Message</span>');
				}
			}
		});
	});

	// Dialog show event handler
	function showAlert(msg, title) {

		// Set default title if undefined
		title = (typeof title === 'undefined') ? '' : title;

		$dialog = $('#alertDialog');

		$dialog.find('.modal-body').html(msg);
		$dialog.find('.modal-title').html(title);
		$dialog.modal();
	}
});

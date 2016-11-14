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

/*
BEGIN DEPRECATED
	// Add New Reference button click handler
	$('#newRef-btn').click(function(e) {
		e.preventDefault();
	
		// Reset insertRef-form inputs
		resetInputFields('insertRef-form');

		// Show insert reference modal, and pass id of textarea as param
		showModal('insertRef-modal');
	});
END DEPRECATED
*/

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
	$('#exportAppendix-btn').click(function() {
		var srid = $(this).attr('data-srid');
		window.open("./content/exportToPDF.php?id=" + srid, "_blank");
	});

	// Handler for Export Attachments to ZIP button
	$('#exportAttachments-btn').click(function() {
		// Not Yet Implemented
	});
});

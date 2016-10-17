$(document).ready(function() {
	$("button[id*='editRef-']").click(function(e) {
		e.preventDefault();

		var id_parts = $(this).attr('id').split('-');
		var link_id = id_parts[1];

		// Navigate to edit page
		location.href = "?page=editRef&lid=" + link_id;

		// Show addEdit-ref form
		//$('#addEdit-ref-dialog').show();
	});

	$("button[id*='delRef-']").click(function(e) {
		e.preventDefault();

		var id_parts = $(this).attr('id').split('-');
		var link_id = id_parts[1];

		$.ajax({
			url: './content/act_appendix.php',
			type: 'post',
			data: {
				'actionType': 1, // delete reference action type
				'linkID': link_id
			},
			success: function(response) {
				location.reload();
			}
		});
	});

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
				'linkID_1': this_linkID,
				'linkID_2': below_linkID
			},
			success: function(response) {
				location.reload();
			}
		});
	});
});

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
});

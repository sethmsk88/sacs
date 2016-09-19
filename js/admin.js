$(document).ready(function () {

	// Add new SR event handler
	addSR_ajax_submit = function(e) {
		e.preventDefault(); // not sure if needed

		$form = $('#addSR-form');

		$.ajax({
			url: './content/act_admin.php',
			method: 'post',
			data: $form.serialize(),
			success: function(response) {
				location.href = "?page=editSR";
			}
		});
	}

	// Edit SR event handler
	editSR_ajax_submit = function(e) {
		e.preventDefault(); // not sure if needed

		alert("Edit not yet implemented!");
	}

	$('.add-btn').click(addSR_ajax_submit);
	$('.edit-sel').change(editSR_ajax_submit);
	
});

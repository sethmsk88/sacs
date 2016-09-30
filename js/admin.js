$(document).ready(function () {

	// Add new SR event handler
	$('.add-btn').click(function(e) {
		e.preventDefault(); // not sure if needed

		// Set hidden form field
		if ($(this).attr('id') == "newCR-btn")
			$('#SRType').val('CR');
		else
			$('#SRType').val('CS');

		$form = $('#addSR-form');

		$.ajax({
			url: './content/act_admin.php',
			method: 'post',
			data: $form.serialize(),
			dataType: 'json',
			success: function(response) {

				$uploadResponse = "";

				// if response is an error, display error
				if (response.hasOwnProperty('errors')) {
					$uploadResponse += response['errors'];					
				} else {
					// No errors, so redirect
					location.href = "?page=editSR&id=" + response['sr_id'];
				}
			}
		});
	});

	// Edit SR event handler
	$('.edit-sel').change(function(e) {
		e.preventDefault(); // not sure if needed

		$el = $(e.target); // convert to jQuery obj
		var SRID = $el.val(); // get ID of selected SR
		location.href = "?page=editSR&id=" + SRID; // redirect to edit page
	});	
});

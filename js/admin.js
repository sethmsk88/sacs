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
					location.href = "?page=editNarrative&id=" + response['sr_id'];
				}
			}
		});
	});

	// Edit SR event handler
	$('.edit-sel').change(function(e) {
		e.preventDefault(); // not sure if needed

		$el = $(e.target); // convert to jQuery obj
		var SRID = $el.val(); // get ID of selected SR
		location.href = "?page=editNarrative&id=" + SRID; // redirect to edit page
	});

	// View button handler
	$("button[id*='view-']").click(function(e) {
		e.preventDefault();

		// get the srid
		var id_parts = $(this).attr('id').split('-');
		var type = id_parts[1];
		var srid = id_parts[2];

		if (type == "narrative") {
			location.href = "?page=narrative&id=" + srid;
		} else if (type == "supplemental") {
			location.href = "?page=subNarrative&id=" + srid;
		} else if (type == "appendix") {
			location.href = "?page=appendix&id=" + srid;
		}
	});

	// Edit button handler
	$("button[id*='edit-']").click(function(e) {
		e.preventDefault();

		// get the srid
		var id_parts = $(this).attr('id').split('-');
		var type = id_parts[1];
		var srid = id_parts[2];

		if (type == "narrative") {
			location.href = "?page=editNarrative&id=" + srid;
		} else if (type == "supplemental") {
			location.href = "?page=editSubNarrative&id=" + srid;
		} else if (type == "appendix") {
			location.href = "?page=appendix&mode=edit&id=" + srid;
		}
	});

	// New SR button handler
	$('#newSR-btn').click(function(e) {
		e.preventDefault();

		location.href = "?page=addSR";
	});

	$('#exportApp-btn').click(function() {
		window.open("./content/exportPortable.php", "_blank");
	});
});

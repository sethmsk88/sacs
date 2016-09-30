$(document).ready(function () {

	// Add SRID to navbar links
	var SRID = $('#SRID').val();
	var navLink1 = $(".navbar-link[href*='editSR']");
	var navLink2 = $(".navbar-link[href*='editSubNarrative']");
	navLink1.attr('href', navLink1.attr('href') + "&id=" + SRID);
	navLink2.attr('href', navLink2.attr('href') + "&id=" + SRID);

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
	
});

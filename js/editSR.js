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
	
});

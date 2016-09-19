$(document).ready(function () {

	// Save button event handler
	$('#save-btn').click(function(e) {
		e.preventDefault();

		var form = $(e).parent('form').get(0);

		$.ajax({
			url: './content/act_editSR.php',
			method: 'post',
			data: $(form).serialize(),
			success: function(response) {
				alert("Changes have been saved!");
			}
		});
	});
	
});

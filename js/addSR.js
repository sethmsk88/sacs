$(document).ready(function() {

	$("#type").change(function() {
		
		// Select the parent row of the input
		$newCS = $('#newCS').closest('div.row');
		$newCR = $('#newCR').closest('div.row');

		// Hide/show Parent Section input
		if ($(this).val() == 's') {
			$newCR.hide();
			$newCS.slideDown();
		}
		else if ($(this).val() == 'r') {
			$newCS.hide();
			$newCR.slideDown();
		} else {
			$newCR.hide();
			$newCS.hide();
		}
	});

	$(".add-btn").click(function(e) {
		e.preventDefault();

		$.ajax({
			url: './content/act_addSR.php',
			method: 'post',
			data: $('#addSR-form').serialize(),
			success: function(response) {
				location.href = "?page=admin";
			}
		});
	});
});

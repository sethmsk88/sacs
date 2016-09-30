$(document).ready(function() {
	
	// Click Handler for subsection radio buttons
	$('input[name="subSection-radio"]').change(function() {

		console.log($(this).val());

		// Select the parent row of the input
		$parentDiv = $('#parentSection').closest('div.row');

		// Hide/show Parent Section input
		if ($(this).val() == 0) {
			console.log("hide");
			$parentDiv.slideUp();
		}
		else if ($(this).val() == '1') {
			console.log("show");
			$parentDiv.slideDown();
		}
	});
});

$(document).ready(function() {

	$("#type").change(function() {
		
		// Select the parent row of the input
		$newCS = $('#newCS').closest('div.row');
		$newCR = $('#newCR').closest('div.row');

		// Hide/show Parent Section input
		if ($(this).val() == 'cs') {
			$newCR.hide();
			$newCS.slideDown();
		}
		else if ($(this).val() == 'cr') {
			$newCS.hide();
			$newCR.slideDown();
		} else {
			$newCR.hide();
			$newCS.hide();
		}
	});
});

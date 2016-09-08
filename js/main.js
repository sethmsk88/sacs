$(document).ready(function (){
	$('.panel').click(function(e) {
		$child = $(this).find('a');

		// Prevent too much recursion error
		if (!$(e.target).is($child)) {
			$child.trigger(e.type);
		}
	});
});

$(document).ready(function() {
	$("button[id*='edit-link-']").click(function(e) {
		e.preventDefault();

		var id_parts = $(this).attr('id').split('-');
		var action = id_parts[0];
		var link_id = id_parts[2];

		// Do something here
		if (action == 'edit') {

		} else if (action == 'add') {
			
		}
	});
});

$(document).ready(function () {

	// Add Section button click handler
	$('#addSection-btn').click(function(e) {
		e.preventDefault();

		var srid = $(this).attr('srid');
		location.href = "?page=addSection&id=" + srid;
	});

	// Add Edit Section Body click handler
	$("button[id*='editSectionBody-'").click(function(e) {
		e.preventDefault();

		// get the sid
		var id_parts = $(this).attr('id').split('-');
		var sid = id_parts[1];

		// navigate to edit section body page
		location.href = "?page=editSectionBody&sid=" + sid;
	});
});

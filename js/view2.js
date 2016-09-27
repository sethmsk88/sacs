$(document).ready(function() {

	// Sub-Narrative button click handler
	$("button[id*='subNarrative-']").click(function() {
		var id_parts = $(this).attr('id').split('-');
		var srid = id_parts[1];

		location.href = "?page=view3&id=" + srid;
	});
});

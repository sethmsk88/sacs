$(document).ready(function() {

	// Sub-Narrative button click handler
	$("button[id*='subNarrative-']").click(function() {
		var id_parts = $(this).attr('id').split('-');
		var srid = id_parts[1];

		location.href = "?page=subNarrative&id=" + srid;
	});

	$("button[id*='appendix-'").click(function() {
		var id_parts = $(this).attr('id').split('-');
		var srid = id_parts[1];

		location.href = "?page=appendix&id=" + srid;
	});

	$('#printEntireDoc-btn').click(function() {
		var srid = $(this).attr('data-srid');
		window.open("./content/exportEntireDoc.php?id=" + srid, "_blank");
	});
});

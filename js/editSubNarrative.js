$(document).ready(function () {

	// Add SRID to navbar links
	var SRID = $('#SRID').val();
	var navLink1 = $(".navbar-link[href*='editSR']");
	var navLink2 = $(".navbar-link[href*='editSubNarrative']");
	navLink1.attr('href', navLink1.attr('href') + "&id=" + SRID);
	navLink2.attr('href', navLink2.attr('href') + "&id=" + SRID);

	// Add Section button click handler
	$('#addSection-btn').click(function() {
		var srid = $(this).attr('srid');
		location.href = "?page=addSection&id=" + srid;
	});
});

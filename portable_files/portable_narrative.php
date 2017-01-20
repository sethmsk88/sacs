<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
?>

<script src="./js/narrative.js"></script>

<?php
	echo $_GET['id'];
?>

<div class="container">
	<div class="row">
		<div class="col-xs-8">
			<h4 id="srHeader"></h4>
		</div>
	</div>

	<!-- Title/Description -->
	<div id="descr"></div>

	<!-- Compliance Status -->
	<table style="width:35%;">
		<tr>
			<td>&nbsp;Non-Compliance</td>
			<td>&nbsp;Partial Compliance</td>
			<td>&nbsp;Compliance</td>
		</tr>
	</table>
	<br>

	<h4>Narrative</h4>
	<div id="narrative"></div>

	<h4>Summary Statement</h4>
	<div id="summary"></div>

	<div class="row" style="margin-top:8px;">
		<div class="col-lg-3">
			<button id="appendix-btn" class="btn btn-primary btn-sm" style="width:100%;">Appendix</button>
		</div>
	</div>
</div>

<script>
	function getURLParameter(sParam) {
	    var sPageURL = window.location.search.substring(1);
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++) {
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam) {
	            return sParameterName[1];
	        }
	    }
	}​

	$(document).ready(function() {
		var srid = getURLParameter('id');

		$.getJSON('../sacs_db.json', {
			tags: ''
		})

	});
</script>

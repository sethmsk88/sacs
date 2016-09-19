<script src="./js/editSR.js"></script>
<link href="./css/editSR.css" rel="stylesheet">

<?php
	// An SR_ID will be available as a GET variable to this page
	$_GET['SR_ID'] = 1; // TESTING

	// TESTING vars
	$header = "C.S. 3.2.8";
	$intro = "This is the intro.";
	$narrative = "This is the narrative.";
	$summary = "This is the summary.";

?>

<div class="container">
	<form>
		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-5 form-group">
				<label for="header">Header</label>
				<input
					type="text"
					name="header"
					id="header"
					class="form-control"
					value="<?= $header ?>">
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="intro">Intro</label>
				<textarea
					name="intro"
					id="intro"
					class="form-control richtext textarea-md"><?= $intro ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="narrative">Narrative</label>
				<textarea
					name="narrative"
					id="narrative"
					class="form-control richtext textarea-lg"><?= $narrative ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="summary">Summary</label>
				<textarea
					name="summary"
					id="summary"
					class="form-control richtext textarea-md"><?= $summary ?></textarea>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-4 col-sm-6 form-group">
				<button id="save-btn" class="btn btn-success" style="width:100%;">Save</button>
			</div>

			<div class="col-lg-4 col-md-5 col-sm-6">
				<button id="view-btn" class="btn btn-primary" style="width:100%;">View Formatted Standard/Requirement</button>
			</div>
		</div>
	</form>
</div>

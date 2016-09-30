<?php
	// Get section body from DB table
	$sel_section = "
		SELECT body
		FROM sacs.section
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_section);
	$stmt->bind_param("i", $_GET['sid']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($sectionBody);
	$stmt->fetch();
?>

<div class="container">
	<form id="editSectionBody-form" action="./content/act_subNarrative.php" method="post">
		<input type="hidden" name="sid" value="<?= $_GET['sid'] ?>">

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="sectionBody">Section Body</label>
				<textarea
					name="sectionBody"
					id="sectionBody"
					class="form-control richtext"><?= $sectionBody ?></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>
		</div>
	</form>
</div>
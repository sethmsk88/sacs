<script src="./js/addSection.js"></script>

<?php
	$sel_sections = "
		SELECT id, name
		FROM sacs.section
		WHERE srid = ?
	";
	$stmt = $conn->prepare($sel_sections);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($sid, $sectionName);
?>

<div class="container">
	<form action="./content/act_subNarrative.php" method="post">
		<input type="hidden" name="srid" value="<?= $_GET['id'] ?>">

		<label>Is this a subsection?</label>
		<div class="row">
			<div class="col-lg-12 form-group">
				<label class="radio-inline">
					<input type="radio" name="subSection-radio" value="0" class="subSection-radio" checked="checked">
					No
				</label>
				<label class="radio-inline">
					<input type="radio" name="subSection-radio" value="1" class="subSection-radio">
					Yes
				</label>
			</div>
		</div>

		<!-- Parent Section Hidden By Default -->
		<div class="row" style="display:none">
			<div class="col-lg-5 form-group">
				<label for="parentSection">Parent Section</label>
				<select name="parentSection" id="parentSection" class="form-control">
					<option value="-1"></option>
					<?php
						// Create options for all sections that are part of this SR
						while ($stmt->fetch()) {
							echo '<option value="' . $sid . '">' . $sectionName . '</option>';
						}
					?>
				</select>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-5 form-group">
				<label for="sectionName">Section Name</label>
				<input type="text" name="sectionName" id="sectionName" class="form-control">
			</div>
		</div>
		<div class="row">
			<div class="col-lg-5">
				<input type="submit" class="btn btn-primary" value="Add Section">
			</div>
		</div>
	</form>
</div>
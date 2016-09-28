<script src="./js/editSubNarrative.js"></script>
<link href="./css/editSubNarrative.css" rel="stylesheet">

<?php
	// Get all sections for this SR
	$sel_sections = "
		SELECT id, name, body
		FROM sacs.section
		WHERE srid = ?
	";
	$stmt = $conn->prepare($sel_sections);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($sid, $sectionName, $body);
?>

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h4>Table of Contents</h4>

			<ol class="begin">
				<?php
					// output sectionName for each section
					while ($stmt->fetch()) {
						echo '<li>' . $sectionName . '</li>';
					}
				?>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<button id="addSection-btn" srid="<?= $_GET['id'] ?>" class="btn btn-primary">Add New Section</button>
		</div>
	</div>
	
	<!-- Divider -->
	<div class="row">
		<div class="divider"></div>
	</div>

	<?php
		// Output each section header and body
		$stmt->data_seek(0); // rewind result object pointer
		$sectionNum = 1; // initialize section counter
		while ($stmt->fetch()) {
			if ($sectionNum == 1)
				$numberingClass = "begin";
			else
				$numberingClass = "continue";
	?>
	<!-- The row class is throwing off the <ol> numbering -->
	<!-- <div class="row"> -->
		<!-- Section Header -->
	<ol class="<?= $numberingClass ?>">
		<li class="h5"><?= $sectionName ?></li>
	</ol>
	<!-- </div> -->
	<!-- <div class="row"> -->
	
	<!-- Section Body -->
	<div class="section-body">
		<?= $body ?>
	</div>
	
	<!-- </div> -->
	<?php
			$sectionNum++;
		}
	?>

	

</div>

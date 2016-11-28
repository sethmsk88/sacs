<?php
	require "./includes/subNarrative_functions.php";

	// Get SR type and SR number
	$sel_sr = "
		SELECT number, sr_type
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srNum, $srType);
	$stmt->fetch();

	// Create SR header
	$srHeader = "";
	if ($srType == 'r')
		$srHeader = "C.R. ";
	else if ($srType == 's')
		$srHeader = "C.S. ";
	$srHeader .= $srNum;
?>

<div class="container">
	<div class="row h4">
		<div class="col-lg-12">
			<?= $srHeader ?>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<h4>Table of Contents</h4>

			<ol class="begin">
				<?php
					// Print sections and their subsections
					$rootID = -1;
					printTOCSection($rootID, $_GET['id'], $conn);
				?>
			</ol>
			<a href="?page=appendix&id=<?= $_GET['id'] ?>">Appendix</a>
		</div>
	</div>

	<!-- Divider -->
	<div class="row">
		<div class="divider"></div>
	</div>

	<?php
		printBodySection($rootID, $_GET['id'], false, $conn);
	?>
</div>

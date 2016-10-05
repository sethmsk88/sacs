<?php
	// Include on ALL pages
	require_once(APP_PATH . "includes/functions.php");

	// Require Login
	if (!isset($loggedIn)) {
		exit;
	} else {
		require_login($loggedIn);
	}
?>

<script src="./js/editSubNarrative.js"></script>
<link href="./css/editSubNarrative.css" rel="stylesheet">

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
	The Roster narratives for <?= $srHeader ?> are organized as follows:
	
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
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<button id="addSection-btn" srid="<?= $_GET['id'] ?>" class="btn btn-primary btn-sm">Add New Section</button>
		</div>
	</div>
	
	<!-- Divider -->
	<div class="row">
		<div class="divider"></div>
	</div>

	<?php
		printBodySection($rootID, $_GET['id'], true, $conn);
	?>
</div>

<!-- This form is purely used as a hidden storage facility for the SRID value passed as a GET variable -->
<form>
	<input type="hidden" name="SRID" id="SRID" value="<?= $_GET['id'] ?>"
</form>


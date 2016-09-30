<script src="./js/view2.js"></script>

<?php
	if (!isset($_GET['id'])) {
		echo '<div class="text-danger">Error: Standard/Requirement ID not found</div>';
		exit;
	}

	// Get SR info from DB
	$sel_sr = "
		SELECT id, number, descr, narrative, summary, sr_type, compliance
		FROM sacs.standard_requirement
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($SRID, $srNum, $descr, $narrative, $summary, $sr_type, $compliance);
	$stmt->fetch();

	// Check to see if any results were returned
	if ($stmt->num_rows == 0) {
		echo '<div class="text-danger">Error: Standard/Requirement does not exist in database</div>';
		exit;
	}

	// create header
	$header = "";
	if ($sr_type == 'r')
		$header .= 'C.R. ';
	else if ($sr_type == 's')
		$header .= 'C.S. ';
	$header .= $srNum;

	// set compliance selection
	$complianceChoice_arr[-1] = "";
	$complianceChoice_arr[0] = "";
	$complianceChoice_arr[1] = "";
	$complianceChoice_arr[$compliance] = "glyphicon glyphicon-remove";
?>


<div class="container">

	<h4><?= $header ?></h4>
	<br>

	<!-- Title/Description -->
	<?= $descr ?>

	<!-- Compliance Status -->
	<table style="width:35%;">
		<tr>
			<td><span class="<?= $complianceChoice_arr[-1] ?>"></span>&nbsp;Non-Compliance</td>
			<td><span class="<?= $complianceChoice_arr[0] ?>"></span>&nbsp;Partial Compliance</td>
			<td><span class="<?= $complianceChoice_arr[1] ?>"></span>&nbsp;Compliance</td>
		</tr>
	</table>
	<br>

	<h4>Narrative</h4>
	<?= $narrative ?>

	<!-- <div class="subheader">Personnel Systems</div>
	<p>
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut id varius tellus, sit amet cursus libero. Aliquam ante purus, facilisis id laoreet ut, imperdiet eget nunc. governed by the <a href="./docs/regulation_10-015.pdf" target="_blank">FAMU Board of Trustees (BOT) Regulation 10.015</a> <a href="./docs/regulation_10-015.pdf" target="_blank" class="ref">[1]</a>. Depending on the position level convallis augue vel neque rhoncus varius. Integer enim eros, porta et nulla non, elementum consequat velit. Nulla dignissim, ante eget tempor iaculis, sapien urna gravida nulla, sed congue metus elit ac dui. Etiam at diam lacinia, ornare leo sed, aliquam nunc. Sed nec ipsum in erat tristique feugiat vel ac velit. Ut ex elit, consequat eu porttitor eu, faucibus eu nibh. Donec quis ligula at lacus dignissim viverra a non dui. In porttitor nisi a nibh facilisis pharetra. Quisque ipsum dolor, posuere at eleifend vel, ultricies nec sapien. Nunc nunc dui, consectetur in tortor a, finibus faucibus velit. 
	</p>
	-->
	
	<!-- Sub-Narrative -->
	<!-- <button id="subNarrative-<?= $SRID ?>" class="btn btn-primary btn-sm">Sub-Narrative</button> -->
	<button id="subNarrative-<?= $SRID ?>" class="btn btn-primary btn-sm">Organizational Chart</button>

	<h4>Summary Statement</h4>
	<?= $summary ?>
	
</div>

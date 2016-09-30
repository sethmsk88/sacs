<script src="./js/editSubNarrative.js"></script>
<link href="./css/editSubNarrative.css" rel="stylesheet">

<?php
	// Check to see if section has children
	function hasChildren($sid, $conn) {
		$sel_sections = "
			SELECT id
			FROM sacs.section
			WHERE parent_id = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("i", $sid);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows == 0)
			return false;
		else
			return true;
	}

	// Print table of contents sections that have $pid as a parent_id
	function printTOCSection($pid, $conn) {
		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM sacs.section
			WHERE parent_id = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("i", $pid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sid, $sectionName, $body, $parent_id);
	
		// For each section with this $pid
		while ($stmt->fetch()) {
			$sectionHasChildren = hasChildren($sid, $conn);

			if ($sectionHasChildren) {
				echo '<li>' . $sectionName . '<ol class="begin">';
			} else {
				echo '<li>' . $sectionName . '</li>';
			}

			printTOCSection($sid, $conn);

			// if this section does have children, close the <li> tag
			if ($sectionHasChildren) {
				echo '</ol></li>';
			}
		}
	}

	function printBodySection($pid, $conn) {

		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM sacs.section
			WHERE parent_id = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("i", $pid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sid, $sectionName, $body, $parent_id);
	
		$sectionNum = 1; // initialize section counter

		// For each section with this $pid
		while ($stmt->fetch()) {
			$sectionHasChildren = hasChildren($sid, $conn);

			if ($sectionNum == 1)
				$numberingClass = "begin";
			else
				$numberingClass = "continue";

			if ($sectionHasChildren) {
				// echo '<li>' . $sectionName . '<ol class="begin">';
				echo '<ol class="' . $numberingClass . '"><li class="h5">' . $sectionName;
			} else {
				// echo '<li>' . $sectionName . '</li>';
				echo '<ol class="' . $numberingClass . '"><li class="h5">' . $sectionName . '</li></ol>';
				echo '<div class="section-body">';
				echo '<button id="editSectionBody-' . $sid . '" class="btn btn-primary">Edit Section Body</button><br />';
				echo $body;
				echo '</div>';
			}

			printBodySection($sid, $conn);

			// if this section does have children, close the <li> tag
			if ($sectionHasChildren) {
				echo '</li></ol>';
			}

			$sectionNum++;
		}
	}

	// Get SR type and SR number
	$sel_sr = "
		SELECT number, sr_type
		FROM sacs.standard_requirement
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

	// Get all root sections for this SR
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
	The Roster narratives for <?= $srHeader ?> are organized as follows:
	
	<div class="row">
		<div class="col-lg-12">
			<h4>Table of Contents</h4>

			<ol class="begin">
				<?php
					// Print sections and their subsections
					$rootID = -1;
					printTOCSection($rootID, $conn);
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
		printBodySection($rootID, $conn);
	?>
</div>

<!-- This form is purely used as a hidden storage facility for the SRID value passed as a GET variable -->
<form>
	<input type="hidden" name="SRID" id="SRID" value="<?= $_GET['id'] ?>"
</form>


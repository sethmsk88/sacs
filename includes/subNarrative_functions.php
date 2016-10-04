<?php
	// Check to see if section has children
	function hasChildren($sid, $srid, $conn) {
		global $TABLE_SECTION;

		$sel_sections = "
			SELECT id
			FROM " . $TABLE_SECTION . "
			WHERE parent_id = ?
				AND srid = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("ii", $sid, $srid);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows == 0)
			return false;
		else
			return true;
	}

	// Print table of contents sections that have $pid as a parent_id
	function printTOCSection($pid, $srid, $conn) {
		global $TABLE_SECTION;		

		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM " . $TABLE_SECTION . "
			WHERE parent_id = ?
				AND srid = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("ii", $pid, $srid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sid, $sectionName, $body, $parent_id);
	
		// For each section with this $pid
		while ($stmt->fetch()) {
			$sectionHasChildren = hasChildren($sid, $srid, $conn);

			if ($sectionHasChildren) {
				echo '<li>' . $sectionName . '<ol class="begin">';
			} else {
				echo '<li>' . $sectionName . '</li>';
			}

			printTOCSection($sid, $srid, $conn);

			// if this section does have children, close the <li> tag
			if ($sectionHasChildren) {
				echo '</ol></li>';
			}
		}
	}

	function printBodySection($pid, $srid, $editable, $conn) {
		global $TABLE_SECTION;

		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM " . $TABLE_SECTION . "
			WHERE parent_id = ?
				AND srid = ?
		";
		$stmt = $conn->prepare($sel_sections);
		$stmt->bind_param("ii", $pid, $srid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($sid, $sectionName, $body, $parent_id);
	
		$sectionNum = 1; // initialize section counter

		// For each section with this $pid
		while ($stmt->fetch()) {
			$sectionHasChildren = hasChildren($sid, $srid, $conn);

			if ($sectionNum == 1)
				$numberingClass = "begin";
			else
				$numberingClass = "continue";

			if ($sectionHasChildren) {
				echo '<ol class="' . $numberingClass . '"><li class="h5">' . $sectionName;
			} else {
				echo '<ol class="' . $numberingClass . '"><li class="h5">' . $sectionName . '</li></ol>';
				echo '<div class="section-body">';

				// Show edit button if editable
				if ($editable) {
					echo '<button id="editSectionBody-' . $sid . '" class="btn btn-primary btn-sm">Edit Section Body</button><br />';
				}

				echo '<span style="font-weight:normal">' . $body . '</span>';
				echo '</div>';
			}

			printBodySection($sid, $srid, $editable, $conn);

			// if this section does have children, close the <li> tag
			if ($sectionHasChildren) {
				echo '</li></ol>';
			}

			$sectionNum++;
		}
	}
?>

<?php
	// Replace all occurrences of $oldStr with $newStr within $text
	function replace_text($text, $oldStr, $newStr) {
		global $file_array;

		while (($pos = stripos($text, $oldStr)) !== FALSE) {

			// Get first occurrence of \" in text starting from position of match
			$endOfLinkPos = stripos(substr($text, $pos), '"');

			// get the rest of the link based on the pattern given in $oldStr
			$match = substr($text, $pos, $endOfLinkPos);

			// if id= exists in string, get all characters from that point on in the string
			$fileidPattern = "fileid=";
			$idPattern = "id=";
			
			// if fileid exists in link
			if ( ($idPos = stripos($match, $fileidPattern)) !== FALSE) {
				$idMatch = substr($match, $idPos + strlen($fileidPattern));

				if (array_key_exists($idMatch, $file_array))
					$tmpNewStr = $newStr . $file_array[$idMatch];

			// Else, if id exists in link
			} else if ( ($idPos = stripos($match, $idPattern)) !== FALSE) {
				$idMatch = substr($match, $idPos + strlen($idPattern));

				// Insert ID into $newStr
				$link_exploded = explode('.', $newStr);
				$link_exploded[0] .= '_' . $idMatch;
				$tmpNewStr = implode('.', $link_exploded);
			} else {
				$tmpNewStr = $newStr;
			}

			// replace text in $text with $newStr starting at position $pos 
			$text = substr_replace($text, $tmpNewStr, $pos, strlen($oldStr) + strlen($idMatch));
		}

		return $text;
	}

	function make_links_portable($text) {

		// key = old link, value = new link
		$linksToModify = array(
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=appendix&amp;id=' => 'appendix.html',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=subNarrative&amp;id=' => 'subNarrative.html',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=narrative&amp;id=' => 'narrative.html',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=' => 'uploads/',
			'http://localhost:8080/bootstrap/apps/sacs/?page=appendix&amp;id=' => 'appendix.html',
			'http://localhost:8080/bootstrap/apps/sacs/?page=subNarrative&amp;id=' => 'subNarrative.html',
			'http://localhost:8080/bootstrap/apps/sacs/?page=narrative&amp;id=' => 'narrative.html',
			'http://localhost:8080/bootstrap/apps/sacs/content/get_file.php?fileid=' => './uploads/',
			'?page=subNarrative&amp;id=' => 'subNarrative.html'
		);

		foreach ($linksToModify as $oldLink => $newLink) {
			$text = replace_text($text, $oldLink, $newLink);
		}

		return $text;
	}

	// Check to see if section has children
	function hasChildren($sid, $srid, $conn) {

		$sel_sections = "
			SELECT id
			FROM " . TABLE_SECTION . "
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

		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM " . TABLE_SECTION . "
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

		$sel_sections = "
			SELECT id, name, body, parent_id
			FROM " . TABLE_SECTION . "
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

				echo '<span style="font-weight:normal">' . make_links_portable($body) . '</span>';
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

<?php
	// Replace all occurrences of $oldStr with $newStr within $text
	function replace_text($text, $oldStr, $newStr) {
		global $file_array;

		while (($pos = stripos($text, $oldStr)) !== FALSE) {

			// Get first occurrence of \" in text starting from position of match
			$endOfLinkPos = stripos(substr($text, $pos), '"');
			$match = substr($text, $pos, $endOfLinkPos);

			// if id= exists in string, get all characters from that point on in the string
			$fileidPattern = "fileid=";
			$idPattern = "id=";
			
			// if fileid exists in link
			if ( ($idPos = stripos($match, $fileidPattern)) !== FALSE) {
				$idMatch = substr($match, $idPos + strlen($fileidPattern));

				if (array_key_exists($idMatch, $file_array))
					$tmpNewStr = $newStr . $file_array[$idMatch];

			// Else, if if exists in link
			} else if ( ($idPos = stripos($match, $idPattern)) !== FALSE) {
				$idMatch = substr($match, $idPos + strlen($idPattern));
				$tmpNewStr = $newStr . $idMatch;
			} else {
				$tmpNewStr = $newStr;
			}

			// replace text in $text with $newStr starting at position $pos 
			$text = substr_replace($text, $tmpNewStr, $pos, strlen($oldStr) + strlen($idMatch));
		}

		return $text;
	}

	function make_links_portable($text) {

		$oldAppendixLink = "http://hrodt.famu.edu/bootstrap/apps/sacs/?page=appendix&amp;id=";
		$newAppendixLink = "./appendix.html?id=";
		$oldSupplementalLink = "http://hrodt.famu.edu/bootstrap/apps/sacs/?page=subNarrative&amp;id=";
		$newSupplementalLink = "./subNarrative.html?id=";
		$oldNarrativeLink = "http://hrodt.famu.edu/bootstrap/apps/sacs/?page=narrative&amp;id=";
		$newNarrativeLink = "./narrative.html?id=";
		$oldFileLink = "http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=";
		$newFileLink = "./uploads/";


		$text = replace_text($text, $oldAppendixLink, $newAppendixLink);
		$text = replace_text($text, $oldSupplementalLink, $newSupplementalLink);
		$text = replace_text($text, $oldNarrativeLink, $newNarrativeLink);
		$text = replace_text($text, $oldFileLink, $newFileLink);

		return $text;
	}

	$sel_files = "
		SELECT file_upload_id, fileName
		FROM ". TABLE_FILE_UPLOAD ."
	";
	$stmt = $conn->prepare($sel_files);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($fileID, $filename);

	// store query results in array 
	$file_array = array();
	while ($stmt->fetch()) {
		$file_array[$fileID] = $filename;
	}

	$str = '<p><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=appendix&amp;id=2" target="_blank">[13]</a>nteger dignissim facilisis lacus. Cras&nbsp; scelerisque magna nisl, a volutpat ipsum laoreet id. Suspendisse interdum, arcu eu ornare elementum, lacus libero convallis dui, non dictum ex lectus et ipsum. Curabitur ut placerat tortor. Phasellus non ante lacus. <a href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=subNarrative&amp;id=2">The Supplemental</a></p>
<p><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=71" target="_blank">[7]</a></p>
<p><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=70" target="_blank">[6]</a></p>
<a href="http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=83" target="_blank">[2]</a>
<a title="Figure 3.2.7-1" href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=narrative&amp;id=7">[See 3.2.7-1]</a><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=90" target="_blank">[2]</a><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=narrative&amp;id=11">[See 3.2.10]</a><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=subNarrative&amp;id=8">[Roster]</a><a href="http://hrodt.famu.edu/bootstrap/apps/sacs/?page=appendix&amp;id=10">Appendix</a>';

	echo '<h3>Before</h3>';
	echo $str;

	echo '<h3>After</h3>';
	echo make_links_portable($str);

?>

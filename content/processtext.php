<?php
	
	function make_links_portable($text) {
		
		$matchWord = "over";
		$newWord = "under";

		// If 'over' exists within the text
		while (($pos = stripos($text, $matchWord)) !== FALSE) {
			$text = substr_replace($text, $newWord, $pos, strlen($matchWord));
		}

		return $text;
	}

	$str = '<p><a href="http://localhost:8080/bootstrap/apps/sacs/?page=appendix&amp;id=2" target="_blank">[13]</a>nteger dignissim facilisis lacus. Cras&nbsp; scelerisque magna nisl, a volutpat ipsum laoreet id. Suspendisse interdum, arcu eu ornare elementum, lacus libero convallis dui, non dictum ex lectus et ipsum. Curabitur ut placerat tortor. Phasellus non ante lacus. <a href="http://localhost:8080/bootstrap/apps/sacs/?page=subNarrative&amp;id=2">The Supplemental</a></p>
<p><a href="http://localhost:8080/bootstrap/apps/sacs/content/get_file.php?fileid=71" target="_blank">[7]</a></p>
<p><a href="http://localhost:8080/bootstrap/apps/sacs/content/get_file.php?fileid=70" target="_blank">[6]</a></p>';

	echo '<h3>Before</h3>';
	echo $str;

	echo '<h3>After</h3>';
	echo make_links_portable($str);

?>

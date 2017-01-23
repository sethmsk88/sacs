<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
?>

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

			// Else, if if exists in link
			} else if ( ($idPos = stripos($match, $idPattern)) !== FALSE) {
				$idMatch = substr($match, $idPos + strlen($idPattern));
				$tmpNewStr = $newStr . $idMatch;
			} else {
				$tmpNewStr = $newStr;
			}

			// echo 'oldStr: '.$oldStr.'<br>';
			// echo 'newStr: '.$tmpNewStr.'<br>';

			// replace text in $text with $newStr starting at position $pos 
			$text = substr_replace($text, $tmpNewStr, $pos, strlen($oldStr) + strlen($idMatch));
		}

		return $text;
	}

	function make_links_portable($text) {

		// key = old link, value = new link
		$linksToModify = array(
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=appendix&amp;id=' => './appendix.html?id=',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=subNarrative&amp;id=' => './subNarrative.html?id=',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/?page=narrative&amp;id=' => './narrative.html?id=',
			'http://hrodt.famu.edu/bootstrap/apps/sacs/content/get_file.php?fileid=' => './uploads/',
			'http://localhost:8080/bootstrap/apps/sacs/?page=appendix&amp;id=' => './appendix.html?id=',
			'http://localhost:8080/bootstrap/apps/sacs/?page=subNarrative&amp;id=' => './subNarrative.html?id=',
			'http://localhost:8080/bootstrap/apps/sacs/?page=narrative&amp;id=' => './narrative.html?id=',
			'http://localhost:8080/bootstrap/apps/sacs/content/get_file.php?fileid=' => './uploads/',
			'?page=subNarrative&amp;id=' => './subNarrative.html?id='
		);

		foreach ($linksToModify as $oldLink => $newLink) {
			$text = replace_text($text, $oldLink, $newLink);
		}

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

	// Get SR info from DB
	$sel_sr = "
		SELECT number, descr, narrative, summary, sr_type, compliance
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		WHERE id = ?
	";
	$stmt = $conn->prepare($sel_sr);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srNum, $descr, $narrative, $summary, $sr_type, $compliance);
	$stmt->fetch();

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
	<div class="row">
		<div class="col-xs-12">
			<h4 id="srHeader"><?= $header ?></h4>
		</div>
	</div>

	<!-- Title/Description -->
	<div id="descr">
		<?= make_links_portable($descr) ?>
	</div>

	<!-- Compliance Status -->
	<table style="width:35%;">
		<tr>
			<td><span class="<?= $complianceChoice_arr[-1] ?>"></span>&nbsp;Non-Compliance</td>
			<td><span class="<?= $complianceChoice_arr[0] ?>"></span>&nbsp;Partial Compliance</td>
			<td><span class="<?= $complianceChoice_arr[1] ?>"></span>&nbsp;Compliance</td>
		</tr>
	</table>
	<br>

	<!-- Narrative -->
	<h4>Narrative</h4>
	<?= make_links_portable($narrative) ?>

	<!-- Summary -->
	<h4>Summary Statement</h4>
	<?= make_links_portable($summary) ?>

	<div class="row" style="margin-top:8px;">
		<div class="col-lg-3">
			<button id="appendix-btn" class="btn btn-primary btn-sm" style="width:100%;">Appendix</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	$("#appendix-btn").click(function() {
		location.href = './appendix.html?id=<?= $_GET["id"] ?>';
	});
</script>

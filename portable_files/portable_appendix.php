<link href="../css/appendix.css" rel="stylesheet">

<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
	require_once("../includes/exportPortable_functions.php");

	function createAppendixRefLink($linkURL, $linkName, $fileID) {
		global $file_array;

		$refLink = "";
		if ($linkURL != "") {
			$refLink = '<a href="'.$linkURL.'" target="_blank">'.$linkName.'</a>';
		} elseif (!empty($fileID)) {
			$refLink = '<a href="./uploads/'. $file_array[$fileID] .'" target="_blank">'.$linkName.'</a>';
		} else {
			$refLink = $linkName;
		}
		return $refLink;
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

	// Get appendix items
	$sel_appendixLinks = "
		SELECT l.appendix_link_id, l.linkName, l.linkURL, l.refNum, f.file_upload_id
		FROM ". TABLE_APPENDIX_LINK ." AS l
		LEFT JOIN ". TABLE_APPENDIX_LINK_HAS_FILE_UPLOAD ." AS lhf
			ON lhf.appendix_link_id = l.appendix_link_id
		LEFT JOIN ". TABLE_FILE_UPLOAD ." AS f
			ON lhf.file_upload_id = f.file_upload_id
		WHERE l.srid = ?
		ORDER BY l.refNum
	";
	$stmt = $conn->prepare($sel_appendixLinks);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkID, $linkName, $linkURL, $refNum, $fileID);
	$numRows = $stmt->num_rows;
?>

<div class="container">
	
	<div class="row">
		<div class="col-md-12">
			<h5>DOCUMENTATION</h5>
		</div>
	</div>
	
	<h5><?= $srNum ?> Appendix</h5>
	<table id="appendix-table-view">
	<?php			
		while ($stmt->fetch()) {
	?>
		<tr>
			<td><?= $refNum ?>.</td>
			<td><?= createAppendixRefLink($linkURL, $linkName, $fileID) ?></td>
		</tr>
	<?php
		}
	?>
	</table>
</div>

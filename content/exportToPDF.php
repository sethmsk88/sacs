<?php
	require_once("../includes/globals.php");
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/db_connect.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bootstrap/apps/shared/api/mpdf60/mpdf.php';

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
		SELECT appendix_link_id, linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE srid = ?
		ORDER BY refNum
	";
	$stmt = $conn->prepare($sel_appendixLinks);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($linkID, $linkName, $linkURL, $refNum);

	if ($srType === 's')
		$srPrefix = 'C.S. ';
	else
		$srPrefix = 'C.R. ';

	$css = '';
	$css .= '<style type="text/css">';
	$css .= 'table {border-collapse:collapse; width:100%;}';
	$css .= 'table tr td {border:1px solid black; padding:4px;}';
	$css .= '</style>';

	$html = '';
	$html .= $css;
	$html .= '<h3>DOCUMENTATION</h3>'; 
	$html .= '<h3>'. $srPrefix . $srNum .' Appendix</h3>';
	$html .= '<table>';

	while ($stmt->fetch()) {
		$html .= '<tr>';
		$html .= '<td>'. $refNum .'</td>';
		$html .= '<td><a href="'. $linkURL .'" target="_blank">'. $linkName .'</a></td>';
		$html .= '</tr>';
	}
	$html .= '</table>';

	$mpdf = new mPDF();
	$mpdf->WriteHTML($html);
	$mpdf->Output();
	exit;
?>

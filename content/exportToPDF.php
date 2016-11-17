<?php
	// Start output buffering
	ob_start();

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
?>

<html>
	<head>
		<link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<script src="/bootstrap/js/bootstrap.min.js"></script>
		<style type="text/css">
			table.appendix {border-collapse:collapse; width:100%;}
			table.appendix tr td {border:1px solid black; padding:4px;}
		</style>
	</head>
	<body>
		<h5>DOCUMENTATION</h5>

		<h5><?= $srNum ?> Appendix</h5>
		<table class="appendix">
		<?php
			while ($stmt->fetch()) {
		?>
			<tr>
				<td><?= $refNum ?>.</td>

				<!-- Create link for reference if $linkURL exists -->
				<?php if ($linkURL != "") { ?>
					<td><a href="<?= $linkURL ?>" target="_blank"><?= $linkName ?></a></td>
				<?php } else { ?>
					<td><?= $linkName ?></td>
				<?php } ?>
			</tr>
		<?php
			}
		?>
		</table>
	</body>
</html>

<?php
	$html = ob_get_clean();

	// echo $html;

	$mpdf = new mPDF();
	$mpdf->WriteHTML($html);
	$mpdf->Output();
	exit;
?>

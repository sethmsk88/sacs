<link href="./css/appendix.css" rel="stylesheet">
<script src="./js/appendix.js"></script>

<?php
	require_once './includes/delete_confirm.php'; // delete confirm modal
	require_once './includes/editRef_modal.php'; // edit reference modal
	require_once './includes/newRef_modal.php'; // new reference modal

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
	$numRows = $stmt->num_rows;
?>

<div class="container">
	<h5>DOCUMENTATION</h5>
	<h5><?= $srNum ?> Appendix</h5>

	<?php
		/*********** Begin Edit Mode ***********/
		if (isset($_GET['mode']) && $_GET['mode'] == 'edit') {
			echo '<table id="appendix-table-edit">';

			$row_i = 0;
			while ($stmt->fetch()) {
				// don't show up arrow on first row
				if ($row_i > 0) {
	?>
		<tr class="up-row row-<?= $row_i ?>">
			<td><button class="btn btn-default up-arrow"><span class="glyphicon glyphicon-arrow-up" style="font-size:22px;"></span></button></td>
			<td></td>
			<td></td>
		</tr>
	<?php
				} // end first row test
	?>
		<tr class="row-<?= $row_i ?>" data-linkid="<?= $linkID ?>">
			<td><?= $refNum ?>.</td>

			<!-- Create link for reference if $linkURL exists -->
			<?php if ($linkURL != "") { ?>
				<td><a href="<?= $linkURL ?>" target="_blank"><?= $linkName ?></a></td>
			<?php } else { ?>
				<td><?= $linkName ?></td>
			<?php }	?>
			<td>
				<button
					id="editRef-<?= $linkID ?>"
					title="Edit Reference"
					class="btn btn-sm btn-warning"
					data-toggle="modal"
					data-target="#editRefModal">
					<span class="glyphicon glyphicon-pencil"></span>
				</button>
				<button
					id="delRef-<?= $linkID ?>"
					title="Delete Reference"
					class="btn btn-sm btn-danger"
					data-toggle="modal"
					data-target="#confirmDelete"
					data-title="Delete Reference"
					data-message="Are you sure you want to delete this reference?">
					<span class="glyphicon glyphicon-remove"></span>
				</button>
			</td>
		</tr>
	<?php
				// don't show down arrow on last row
				if ($row_i < $numRows - 1) {
	?>
		<tr class="down-row row-<?= $row_i ?>">
			<td><button class="btn btn-default down-arrow"><span class="glyphicon glyphicon-arrow-down"></span></button></td>
			<td></td>
			<td></td>
		</tr>
	<?php
				} // end last row test
				$row_i++;
			} // end while loop
	?>
		<tr>
			<td colspan="3">
				<button
					id="newRef-btn"
					title="Add New Reference"
					class="btn btn-success"
					data-toggle="modal"
					data-target="#newRefModal"
					style="width:100%">
					<span class="glyphicon glyphicon-plus"></span> New
				</button>
			</td>
		</tr>
	<?php
		} /*********** End Edit Mode ***********/
		else {
			
			/*********** Begin View Mode ***********/
			echo '<table id="appendix-table-view">';
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
			} // End while loop
		} /*********** End View Mode ***********/
	?>
	</table>
</div>

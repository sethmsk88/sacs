<link href="./css/appendix.css" rel="stylesheet">
<script src="./js/appendix.js"></script>

<?php
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
	<h5><?= $srNum ?> Appendix A</h5>

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
			<td><button id="editRef-<?= $linkID ?>" class="btn btn-sm btn-primary">Edit Reference</button></td>
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


	

	<!-- <ol>
		<li><a href="http://www.leg.state.fl.us/Statutes/index.cfm?App_mode=Display_Statute&Search_String=&URL=1000-1099/1001/Sections/1001.706.html" target="_blank">Section 7 of the Constitution of the State of Florida</a></li>
	
		<li><a href="http://www.flbog.edu/documents_regulations/regulations/1_001_PowersandDuties_Final.pdf" target="_blank">Fl. BOG Regulation 1.001 University Board of Trustees Powers and Duties 1.001</a></li>
	
		<li><a href="http://www.famu.edu/index.cfm?BOT&AbouttheTrustees" target="_blank">FAMU BOT</a></li>
	
		<li><a href="http://www.famu.edu/regulations/Reg 1 021 12-2012.pdf" target="_blank">University Regulation 1.021, Authority of the President</a></li>
	
		<li><a href="http://president.famu.edu/pdfs/UniversityOrgChart.pdf" target="_blank">FAMU Organizational Chart of Direct reports</a></li>
	
		<li><a href="http://www.famu.edu/regulations/UserFiles/Image/Regulation_1.017_Succession_9-17.pdf" target="_blank">University Regulation 1.017, Succession to Administration Authority and Responsible to President</a></li>
	
		<li><a href="http://president.famu.edu/executivestaff.html" target="_blank">FAMU Website, Homepage, Administration</a></li>
		
		<li><a href="https://irattler.famu.edu/psp/famepprd/EMPLOYEE/EMPL/h/?tab=PAPP_GUEST" target="_blank">iRattler Portal</a></li>
	</ol> -->
</div>

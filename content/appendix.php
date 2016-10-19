<link href="./css/appendix.css" rel="stylesheet">
<script src="./js/appendix.js"></script>

<?php
	require_once './includes/delete_confirm.php'; // delete confirm modal
	require_once './includes/editRef_modal.php'; // edit reference modal

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
		/*echo 'TESTING regex<br />';
		//$phrase = 'Hello there, here is a <a href="http://www.google.com" target="_blank">[5]</a>.';
		$phrase = '
		<div id="lipsum">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dictum libero tellus, ac ultricies dolor viverra vitae. Aenean auctor, neque sed convallis dictum, arcu est iaculis tortor, non iaculis risus dui ullamcorper erat. Praesent&nbsp;<a href="http://www.famu.edu/index.cfm?BOT&amp;AbouttheTrustees" target="_blank">[2]</a> et quam nibh. Pellentesque pretium, lacus semper aliquam consequat, magna magna sollicitudin sapien, aliquam tristique turpis erat volutpat tortor. Mauris ligula turpis, eleifend eu enim ac, pulvinar gravida odio. Praesent ac lectus gravida, sodales felis cursus, cursus lorem. Sed viverra dolor velit. Curabitur lacus felis, hendrerit ac quam ac, luctus ullamcorper leo. Praesent mauris est, malesuada id iaculis quis, faucibus eget lectus. Etiam elementum, magna quis fringilla ultrices, quam arcu auctor tortor, vel consectetur dui quam nec metus.</p>
<p>Maecenas sit amet turpis tristique, elementum sem eu, maximus felis. Nunc tortor ipsum, pretium et orci sit amet, tristique accumsan nisl. Aliquam erat volutpat. Cras sagittis blandit massa at iaculis. Nam gravida nisi in malesuada bibendum. Quisque luctus nisi eu lorem imperdiet euismod. Fusce egestas interdum sem sed ultricies. Morbi rhoncus justo ante, fermentum pellentesque purus faucibus ut. Sed lacinia massa sagittis nunc malesuada, non feugiat enim egestas.</p>
<p>Maecenas sed nibh sit amet ligula blandit tristique id et sem. Ut ut erat dolor. Aenean dui nunc, vulputate a ex vitae, maximus aliquam dui. Mauris porta tristique dictum. Aenean tincidunt elit ut ipsum tempus condimentum. Etiam at orci magna. Maecenas vulputate molestie nulla a condimentum. In in erat mollis, dignissim est a, faucibus sem. Proin nec massa velit. Sed et massa turpis. Nulla sodales arcu sit amet<a href="http://www.flbog.edu/documents_regulations/regulations/1_001_PowersandDuties_Final.pdf" target="_blank">[3]</a> purus iaculis dictum nec a mi. Morbi posuere purus nisl. Vivamus molestie, ligula bibendum pharetra vehicula, lectus sapien ultrices dui, eu consequat sem leo vel magna. Maecenas et erat a nisi ornare elementum et at turpis. Cras commodo lacus in sapien laoreet sagittis.</p>
</div>
<p>&nbsp;</p>';
		echo $phrase . '<br />';
		$refNum = 3;
		// $refLink_pattern = "/<a(.)*\[" . $refNum . "+\]<\/a>/";
		$refLink_pattern = '/<a href=""(.)*\[[0-9]+\]<\/a>/';
		echo preg_replace($refLink_pattern, '[?]', $phrase);*/
	?>

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

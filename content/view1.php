<?php
	// Get all SRs
	$sel_SR = "
		SELECT id, number, descr, narrative, summary, sr_type, compliance
		FROM " . TABLE_STANDARD_REQUIREMENT . "
		ORDER BY sr_type, number
	";
	$stmt = $conn->prepare($sel_SR);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($SRID, $srNum, $descr, $narrative, $summary, $srType, $compliance);

	// Put SRs into appropriate array
	$cr_arr = array();
	$cs_arr = array();
	while ($stmt->fetch()) {
		if ($srType == 'r')
			$cr_arr[$SRID] = $srNum;
		else
			$cs_arr[$SRID] = $srNum;
	}

	
	
?>

<div class="container">
	
	<!-- First button -->
	<div class="row">
		<div class="col-lg-4 col-md-5 col-sm-6">
			<div id="collapse-group-0" class="panel-group">
				<div class="panel">
					<div class="panel-heading">
						<a
							data-toggle="collapse"
							data-parent="#collapse-group-0"
							href="#collapse-0">
							Core Requirements
						</a>
					</div>
					<div id="collapse-0" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
								foreach ($cr_arr as $cr_SRID => $cr_srNum) {
									echo '<div>';
									echo '<a href="?page=view2&id=' . $cr_SRID . '">C.R. ' . $cr_srNum . '</a>';
									echo '</div>';
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Second button -->
	<div class="row">
		<div class="col-lg-4 col-md-5 col-sm-6">
			<div id="collapse-group-1" class="panel-group">
				<div class="panel">
					<div class="panel-heading">
						<a
							data-toggle="collapse"
							data-parent="#collapse-group-1"
							href="#collapse-1">
							Comprehensive Standards
						</a>
					</div>
					<div id="collapse-1" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
								foreach ($cs_arr as $cs_SRID => $cs_srNum) {
									echo '<div>';
									echo '<a href="?page=view2&id=' . $cs_SRID . '">C.S. ' . $cs_srNum . '</a>';
									echo '</div>';
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

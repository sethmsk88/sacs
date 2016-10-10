<?php
	// Include on ALL pages
	require_once(APP_PATH . "includes/functions.php");

	// Require Login
	if (!isset($loggedIn)) {
		exit;
	} else {
		require_login($loggedIn);
	}

	// Make sure the link_id GET var exists
	if (!isset($_GET['lid'])) {
		echo '<div class="text-danger">Error: Link ID not found</div>';
		exit;
	}

	$sel_link = "
		SELECT srid, linkName, linkURL, refNum
		FROM " . TABLE_APPENDIX_LINK . "
		WHERE appendix_link_id = ?
	";
	$stmt = $conn->prepare($sel_link);
	$stmt->bind_param("i", $_GET['lid']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($srid, $linkName, $linkURL, $refNum);
	$stmt->fetch();
?>

<div class="container">
	<form
		id="addEdit-ref-form"
		method="post"
		action="./content/act_editRef.php">

		<div class="row">
			<div class="col-lg-12">
				<h4>Editing Reference #<?= $refNum ?></h4>
			</div>
		</div>

<!--
		<div class="row">
			<div class="col-lg-12 form-group">
				<label>Type</label><br />
				<label class="radio-inline">
					<input type="radio" name="refType" id="refType" value="0">URL
				</label>
				<label class="radio-inline">
					<input type="radio" name="refType" id="refType" value="1">File
				</label>
			</div>
		</div>
-->

		<div class="row">
			<div class="col-lg-12 form-group">
				<label for="refName">Reference Name</label>
				<input
					 type="text"
					 name="refName"
					 id="refName"
					 class="form-control"
					 value="<?= $linkName ?>">
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12" class="form-group">
				<label for="refURL">Reference URL</label>
				<input
					type="text"
					name="refURL"
					id="refURL"
					class="form-control"
					value="<?= $linkURL ?>">
			</div>
		</div>

		<input type="hidden" name="link_id" value="<?= $_GET['lid'] ?>">
		<input type="hidden" name="srid" value="<?= $srid ?>">

		<div class="row" style="margin-top:12px;">
			<div class="col-lg-12">
				<input type="submit" class="btn btn-primary" value="Save">
			</div>
		</div>
	</form>
</div>

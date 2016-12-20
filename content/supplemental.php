<?php
	require_once(APP_PATH . "includes/functions.php");
	require_once("./includes/addSection_modal.php");


	// Require Login
	if (!isset($loggedIn)) {
		exit;
	} else {
		require_login($loggedIn);
	}

	// Require GET variable, id
	if (!isset($_GET['id'])) {
		exit("Error: Missing SRID");
	}

	class Section {
		private $sr_id;
		private $section_id;
		private $parent_id;
		private $name;
		private $content;

		public function __construct($sr_id, $section_id, $name, $content, $parent_id) {
			$this->sr_id = $sr_id;
			$this->section_id = $section_id;
			$this->parent_id = $parent_id;
			$this->name = $name;
			$this->content = $content;
		}

		public function setName($name) {
			$this->name = $name;
		}

		public function setContent($content) {
			$this->content = $content;
		}

		public function getSRID() {
			return $this->sr_id;
		}

		public function getSectionID() {
			return $this->section_id;
		}

		public function getName() {
			return $this->name;
		}

		public function getContent() {
			return $this->content;
		}

		public function getParentID() {
			return $this->parent_id;
		}

		public function getEditSectionBtn() {
			// not yet implemented
		}

		// Output Section Name for Table of Contents 
		public function getTOCItem() {
			// not yet implemented
		}

		public function getContentHeader() {
			// not yet implemented
		}

		public function getContentBody() {
			// not yet implemented
		}
	}

	// Get all sections for this srid
	$sel_sections = "
		SELECT id, srid, name, body, parent_id
		FROM ". TABLE_SECTION ."
		WHERE srid = ?
	";
	$stmt = $conn->prepare($sel_sections);
	$stmt->bind_param("i", $_GET['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($section_id, $sr_id, $name, $content, $parent_id);

	// Create Section objects and store them in an array
	$sections = array();
	while ($stmt->fetch()) {
		$section = new Section($sr_id, $section_id, $name, $content, $parent_id);
		array_push($sections, $section);
	}
?>

<div class="container">
	<h4>[SR Header]</h4>
	
	<div class="row">
		<div class="col-md-4">
			<form name="supName-form" id="supName-form" role="form">
				<label for="">Name of Supplemental</label>
				<input
					name="supName"
					type="text"
					class="form-control"
					placeholder="(e.g. Roster, Table/Figures)">
			</form>
		</div>
	</div>

	<h4>Table of Contents</h4>
	<?php
		// Print Table of Contents
		$counter = 1;
		foreach ($sections as $section) {
	?>
	<div class="row">
		<div class="col-md-8">
			<?= $counter ?>. <?= $section->getName() ?>
		</div>
		<div class="col-md-4">
			<button
				title="Add New Nested Section"
				class="btn btn-success"
				data-toggle="modal"
				data-target="#sectionAction"
				data-action="1"
				data-sectionid="<?=$section->getSectionID()?>">+</button>
			<button class="btn btn-warning">/</button>
			<buttton class="btn btn-danger">X</buttton>
		</div>
	</div>
	<?php
			$counter++;
		} // End - Print Table of Contents
	?>

	<div class="row">
		<div class="col-md-12">
			<button
				title="Add New Section"
				class="btn btn-success"
				data-toggle="modal"
				data-target="#addSection-modal"
				data-action="0">+ New Section</button>
		</div>
	</div>

	
</div>

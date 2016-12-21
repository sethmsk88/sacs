<?php
	require_once(APP_PATH . "includes/functions.php");
	require_once("./includes/sectionAction_modal.php");


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
		private $orderNum;
		private $hasChild;

		public function __construct($section_id) {
			// get section info from database and set private values
			$sel_section = "
				SELECT srid, name, body, parent_id, hasChild, orderNum
				FROM ". TABLE_SECTION ."
				WHERE id = ?
			";
			$stmt = $conn->prepare();
			$stmt->bind_param('i', $section_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($sr_id, $name, $content, $parent_id, $hasChild, $orderNum);
			$stmt->fetch();

			$this->sr_id = $sr_id;
			$this->section_id = $section_id;
			$this->parent_id = $parent_id;
			$this->name = $name;
			$this->content = $content;
			$this->hasChild = $hasChild;
			$this->orderNum = $orderNum;
		}

		public function __construct($sr_id, $section_id, $name, $content, $parent_id, $hasChild, $orderNum) {
			$this->sr_id = $sr_id;
			$this->section_id = $section_id;
			$this->parent_id = $parent_id;
			$this->name = $name;
			$this->content = $content;
			$this->hasChild = $hasChild;
			$this->orderNum = $orderNum;
		}

		public function setName($name) {
			$this->name = $name;
		}

		public function setContent($content) {
			$this->content = $content;
		}

		public function setOrderNum($orderNum) {
			$this->orderNum = $orderNum;
		}

		public function setHasChild($hasChild) {
			$this->hasChild = $hasChild;
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

		public function getHasChild() {
			return $this->hasChild;
		}

		public function getOrderNum() {
			$this->orderNum;
		}
	}

	function printTOCSection($section_id) {
		/*
			get all root sections from database, then iterate over them?

			get section from database and create section object using $section_id

			print <li> . sectionName
			if section has a child
				printTOCSection

		*/

		echo '<li>' . $section->getName();
		if ($section->getParentID() > -1) {

		}
	}

	function printTOCSection($section) {
		echo '<li>' . $section->getName();
		if ($section->getParentID() > -1) {

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
	<ul class="list-unstyled">
		<?php
			// Print Table of Contents
			$counter = 1;
			foreach ($sections as $section) {
		?>
		<li>
			<div class="row">
				<div class="col-md-8">
					<?= $counter ?>. <?= $section->getName() ?>
				</div>
				<div class="col-md-4">
					<button
						title="Add New Nested Section"
						class="btn btn-success"
						data-toggle="modal"
						data-target="#sectionAction-modal"
						data-action="1"
						data-sectionid="<?= $section->getSectionID() ?>"
						data-sectionname="<?= $section->getName() ?>">+</button>
					<button class="btn btn-warning">/</button>
					<buttton class="btn btn-danger">X</buttton>
				</div>
			</div>
			<?php
					$counter++;
				} // End - Print Table of Contents
			?>
		</li>
	</ul>

	<div class="row">
		<div class="col-md-12">
			<button
				title="Add New Section"
				class="btn btn-success"
				data-toggle="modal"
				data-target="#sectionAction-modal"
				data-action="0">+ New Section</button>
		</div>
	</div>

	
</div>

<?php
/*
	database section Table
		add columns
			orderNum int
			hasChild boolean default false
		alter columns
			parent_id int default -1
		
	when adding a section
		if it's a root section
			parent_id = [default value]
			hasChild = [default value]
		if it's a child section
			parent_id = [id of parent]
			child_id = [default value]
			update parent section record
				hasChild = true

	when deleting a section
		if hasChild == true
			display message telling User that all nested sections must be deleted before this section can be deleted

	

*/
?>

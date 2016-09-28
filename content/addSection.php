<div class="container">
	<form action="./content/act_subNarrative.php" method="post">
		<input type="hidden" name="srid" value="<?= $_GET['id'] ?>">

		<div class="row">
			<div class="col-lg-4 form-group">
				<label for="sectionName">Section Name</label>
				<input type="text" name="sectionName" id="sectionName" class="form-control">
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<input type="submit" class="btn btn-primary" value="Add Section">
			</div>
		</div>
	</form>
</div>
<script src="./js/admin.js"></script>

<div class="container">

	<form id="addSR-form">
		<div class="row">
			<div class="col-lg-4 col-md-5">
				<div class="form-group">
					<label for="newCR">Add a new Core Requirement</label>
					<div class="input-group">
						<input type="text" class="form-control" id="newCR">
						<span class="input-group-btn">
							<button type="button" class="btn btn-primary add-btn">Add</button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-lg-offset-2 col-md-5 col-md-offset-1">
				<label for="newCS">Add a new Comprehensive Standard</label>
				<div class="input-group">
					<input type="text" class="form-control" id="newCS">
					<span class="input-group-btn">
						<button type="button" class="btn btn-primary add-btn">Add</button>
					</span>
				</div>
			</div>
		</div>
	</form>

	<form id="editSR-form">
		<div class="row">
			<div class="col-lg-6 col-md-6">
				<div class="form-group">
					<label for="existingCR">Edit an existing Core Requirement</label>
					<select class="form-control edit-sel" id="existingCR">
						<option value="-1" selected="selected">Select...</option>
						<option value="0">C.R. 1.1.1</option>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6">
				<div class="form-group">
					<label for="existingCS ">Edit an existing Comprehensive Standard</label>
					<select class="form-control edit-sel" id="existingCS">
						<option value="-1" selected="selected">Select...</option>
						<option value="0">C.S. 3.2.8</option>
					</select>
				</div>
			</div>
		</div>
	</form>
</div>

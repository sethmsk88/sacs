<script src="./js/addSR.js"></script>

<div class="container">
	<form>
		<div class="row">
			<div class="col-lg-4 form-group">
				<label for="type">Select Type</label>
				<select name="type" id="type" class="form-control">
					<option value=""></option>
					<option value="cs">Comprehensive Standard (C.S.)</option>
					<option value="cr">Core Requirement (C.R.)</option>
				</select>
			</div>
		</div>
		
		<div class="row" style="display:none;">
			<div class="col-lg-4 col-md-5">
				<div class="form-group">
					<label for="newCR">Add a new Core Requirement</label>
					<div class="input-group">
						<input
							type="text"
							name="newCR"
							id="newCR"
							class="form-control"
							placeholder="(example: 3.2.6)">
						<span class="input-group-btn">
							<button type="button" id="newCR-btn" class="btn btn-primary add-btn">Add</button>
						</span>
					</div>
				</div>
			</div>
		</div>

		<div class="row" style="display:none;">
			<div class="col-lg-4 col-md-5">
				<label for="newCS">Add a new Comprehensive Standard</label>
				<div class="input-group">
					<input
						type="text"
						name="newCS"
						id="newCS"
						class="form-control"
						placeholder="(example: 3.2.6)">
					<span class="input-group-btn">
						<button type="button" id="newCS-btn" class="btn btn-primary add-btn">Add</button>
					</span>
				</div>
			</div>
		</div>
	</form>
</div>

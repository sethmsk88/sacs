<div
	class="modal fade"
	id="editRefModal"
	role="dialog"
	aria-labelledby=""
	aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button
					type="button"
					class="close"
					data-dismiss="modal"
					aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">Edit Reference</h4>
			</div>
			<div class="modal-body">
				<form
					name="editRef-form"
					id="editRef-form"
					role="form">

					<div class="row">
						<div class="col-lg-12 form-group">
							<label for="refName">Reference Name</label>
							<input
								type="text"
								name="refName"
								id="refName"
								class="form-control">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12" class="form-group">
							<label for="refURL">Reference URL</label>
							<input
								type="text"
								name="refURL"
								id="refURL"
								class="form-control">
						</div>
					</div>
					<input type="hidden" name="linkID" id="linkID" value="">
				</form>
			</div>

			<div class="modal-footer">
				<button
					type="button"
					class="btn btn-default"
					data-dismiss="modal">
					Cancel
				</button>
				<button
					type="button"
					class="btn btn-primary"
					id="editSubmit">
					Save Changes
				</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	// Dialog show event handler
	$('#editRefModal').on('show.bs.modal', function (e) {

		$clickedButton = $(e.relatedTarget);

		// Get ID of button that was clicked to show modal
		$buttonID = $clickedButton.attr('id');
		var id_parts = $buttonID.split('-');
		var link_id = id_parts[1];

		// get link information
		$.ajax({
			type: 'post',
			url: './content/act_getRefInfo.php',
			data: {
				'linkID': link_id
			},
			dataType: 'json',
			success: function(response) {
				// populate form fields with link info
				$('#refName').val(response['linkName']);
				$('#refURL').val(response['linkURL']);
			}
		});

		/*
			NOTE: Can pass in modal attributes using attributes in the 
			button tag if that method is preferred. (See lines below)
		*/
		// $message = $(e.relatedTarget).attr('data-message');
		// $(this).find('.modal-body p').html($message);
		// $title = $(e.relatedTarget).attr('data-title');
		// $(this).find('.modal-title').text($title);
	});

	// Form submit handler
	$('#editRefModal').find('.modal-footer #editSubmit').on('click', function() {
		var id_parts = $buttonID.split('-');
		var link_id = id_parts[1];

		//  update link information in table
		$.ajax({
			url: './content/act_appendix.php',
			type: 'post',
			data: {
				'linkID': link_id,
				'actionType': 2, // edit reference action type
				'refName': $('#refName').val(),
				'refURL': $('#refURL').val()
			},
			success: function(response) {
				location.reload();
			}
		});
		
	});

</script>

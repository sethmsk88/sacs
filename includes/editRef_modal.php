<?php
	require_once("./includes/globals.php");
?>

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
					role="form"
					enctype="multipart/form-data">

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

					<div id="box-1" style="display:none;">
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

						<div class="row" style="margin-top:12px;">
							<div class="col-lg-12" class="form-group">
								<button id="attachFile-btn" class="btn btn-primary">Attach File</button>
							</div>
						</div>
					</div>

					<div id="box-2" style="display:none;">
						<div class="row">
							<div class="col-lg-12">
								<label>Attached File:</label>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<span id="refFile-link"></span>
								<button id="removeFile-btn" class="btn btn-link btn-lg" title="Remove File" data-fileid="" data-linkid=""><span class="glyphicon glyphicon-remove text-danger"></span></button>
							</div>
						</div>
					</div>

					<div id="box-3" style="display:none;">
						<div class="row">
							<div class="col-lg-12" class="form-group">
								<label for="fileToUpload">Select a file to upload</label>
								<div class="input-group">
									<span class="input-group-btn">
										<span class="btn btn-primary btn-file">
											Browse <input type="file" name="fileToUpload" id="fileToUpload">
										</span>
									</span>
									<input type="text" class="form-control" readonly="readonly">
								</div>

							</div>
						</div>
					</div>


					<input type="hidden" name="linkID" id="linkID" value="">
				</form>

				<div class="row">
					<div id="ajax_response" class="col-lg-12" style="margin-top:8px;">
						<!-- To Be Filled with AJAX response messages --> 
					</div>
				</div>
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
	
	var resetModalFields = function() {
		$('#editRefModal #box-1').hide();
		$('#editRefModal #box-2').hide();
		$('#editRefModal #box-3').hide();

		$('#editRefModal input').val("");
	}

	// Dialog show event handler
	$('#editRefModal').on('show.bs.modal', function (e) {

		resetModalFields();

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
				$('#editRefModal #refName').val(response['linkName']);
				$('#editRefModal #refURL').val(response['linkURL']);

				// If this is a file reference, create a file link
				if (response.hasOwnProperty('fileID')) {
					$('#editRefModal #box-2').show();

					var refURL = "<?= APP_GET_FILE_PAGE ?>" + "?fileid=" + response['fileID'];

					var refFileLink = '<a href="'+ refURL +'" target="_blank">' + response['fileName'] + '</a>';

					$('#editRefModal #refFile-link').html(refFileLink);				

					// populate remove button with file_id and link_id
					$('#editRefModal #removeFile-btn').attr('data-fileid', response['fileID']);
					$('#editRefModal #removeFile-btn').attr('data-linkid', response['linkID']);
				} else {
					$('#editRefModal #box-1').show();
				}
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

		var formData = new FormData($('#editRef-form')[0]);
		formData.append('refLinkID', link_id);
		formData.append('actionType', 2); // edit reference action type

		$.ajax({
			url: './content/act_appendix.php',
			type: 'POST',
			data: formData,
			dataType: 'json',
			contentType: false,
			processData: false,
			success: function(response) {

				// If there are errors
				if (response.hasOwnProperty('errors') &&
					response['errors'].length > 0) {

					var openErrTag = '<div class="text-danger">';
					var closeErrTag = '</div>';
					var errMsg = openErrTag + "<b>An Error Has Occurred!</b>" + closeErrTag;

					for (var i=0; i < response['errors'].length; i++) {
						errMsg += openErrTag + response['errors'][i] + closeErrTag;
					}

					$('#editRefModal #ajax_response').html(errMsg);
				} else {
					location.reload();
				}
			}
		});	
	});

	// Attach File button click handler
	$('#editRefModal #attachFile-btn').click(function(e) {
		e.preventDefault();

		$('#editRefModal #box-1').fadeOut(function() {
			$('#editRefModal #box-3').fadeIn();
		});
	});

	// Remove file button click handler
	$('#editRefModal #removeFile-btn').click(function(e) {
		e.preventDefault();

		$removeBtn = $(this);

		$.ajax({
			url: './content/act_appendix.php',
			type: 'post',
			data: {
				'actionType': 3, // remove file action type
				'fileID': $removeBtn.attr('data-fileid'),
				'linkID': $removeBtn.attr('data-linkid')
			},
			success: function() {
				// clear the ids from the remove button
				$removeBtn.attr('data-fileid', '');
				$removeBtn.attr('data-linkid', '');

				// clear the old refURL from the modal form
				$('#editRefModal #editRef-form #refURL').val('');

				$('#editRefModal #box-2').fadeOut(function() {
					$('#editRefModal #box-3').fadeIn();
				});
			}
		});
	});
</script>

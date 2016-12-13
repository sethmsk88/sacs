<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<form
			name="img-form"
			method="POST"
			enctype="multipart/form-data">

			<label for="fileToUpload">Select a file to upload</label>
			<div class="input-group">
				<span class="input-group-btn">
					<span class="btn btn-primary btn-file">
						Browse <input type="file" name="fileToUpload" id="fileToUpload">
					</span>
				</span>
				<input type="text" class="form-control" readonly="readonly">
			</div>

			<textarea name="myTextarea"></textarea>

			<input type="submit">
		</form>
	</body>
</html>

<script>
var richtext_plugins = 'advlist anchor autolink charmap code contextmenu hr image imagetools link lists paste preview searchreplace spellchecker table textcolor wordcount';

/*tinymce.init({
	selector: 'textarea',
	height: 200,
	plugins: richtext_plugins,
	paste_data_images: true,
	elementpath: false,
	convert_urls: false
});*/

tinymce.init({
	selector: 'textarea',
	height: 200,
	plugins: richtext_plugins,
	paste_data_images: true,
	elementpath: false,
	convert_urls: false,
	toolbar: 'undo redo | stylesheet | bold italic | link image | imageupload',
	// toolbar : "imageupload",
	setup: function(editor) {
		var inp = $('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
		$(editor.getElement()).parent().append(inp);

		inp.on("change",function(){

			var input = inp.get(0);
			var file = input.files[0];

			var formData = new FormData();
			formData.append('fileToUpload', file);

			$.ajax({
				type: 'POST',
				url: './content/act_uploadImages.php',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false,
				success: function(response) {
					fileURL = response['fileURL'];
					editor.insertContent('<img src="'+ fileURL +'"/>');
				}
			});			
		});

		editor.addButton( 'imageupload', {
			text:"IMAGE",
			icon: false,
			title: "Insert image",
			onclick: function(e) {
				inp.trigger('click');
			}
		});
	}
});

/*tinymce.init({
	selector: 'textarea',
	height: 200,
	plugins: richtext_plugins,
	paste_data_images: true,
	elementpath: false,
	convert_urls: false,
	toolbar: 'undo redo | stylesheet | bold italic | link image | imageupload',
	// toolbar : "imageupload",
	setup: function(editor) {
		var inp = $('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
		$(editor.getElement()).parent().append(inp);

		inp.on("change",function(){
			var input = inp.get(0);
			var file = input.files[0];
			var fr = new FileReader();
			fr.onload = function() {
				var img = new Image();
				img.src = fr.result;
				editor.insertContent('<img src="'+img.src+'"/>');
				inp.val('');
			}
			fr.readAsDataURL(file);
		});

		editor.addButton( 'imageupload', {
			text:"IMAGE",
			icon: false,
			title: "Insert image",
			onclick: function(e) {
				inp.trigger('click');
			}
		});
	}
});*/



// Form Submit Handler for image upload form 
$('[name="img-form"]').submit(function(e) {
	e.preventDefault();

	/*tinymce.activeEditor.uploadImages(function(success) {
		$.post('./content/act_uploadImages.php', tinymce.activeEditor.getContent()).done(function() {
			console.log("Uploaded images and posted content as an AJAX request.");
		});
	});*/

	var formData = new FormData($(this)[0]);

	$.ajax({
		type: 'POST',
		url: './content/act_uploadImages.php',
		data: formData,
		dataType: 'json',
		contentType: false,
		processData: false,
		success: function(response) {
			console.log(response);
		}
	});
});
</script>

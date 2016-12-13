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

var menu_config = {
	file: {title: 'File', items: 'newdocument'},
	edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
	insert: {title: 'Insert', items: 'link imageupload | template hr'},
	view: {title: 'View', items: 'visualaid | preview'},
	format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
	table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
	tools: {title: 'Tools', items: 'spellchecker, code'}
};

var toolbar_config = 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link imageupload';

tinymce.init({
	selector: 'textarea',
	height: 200,
	plugins: richtext_plugins,
	menu: menu_config,
	paste_data_images: true,
	elementpath: false,
	convert_urls: false,
	toolbar: toolbar_config,
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

		editor.addButton('imageupload', {
			icon: 'mce-ico mce-i-image',
			title: "Insert image",
			onclick: function(e) {
				inp.trigger('click');
			}
		});

		editor.addMenuItem('imageupload', {
			text: 'Insert Image',
			icon: 'mce-ico mce-i-image',
			context: 'insert',
			onclick: function(E) {
				inp.trigger('click');
			}
		});
	}
});

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

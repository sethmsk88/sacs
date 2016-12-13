/**
 *	Center the calling element on the screen
 */
jQuery.fn.center = function() {
	this.css("position", "absolute");
	this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2 - 100) + $(window).scrollTop()) + "px");
	this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	return this;
}

// Append val to the calling HTML element
jQuery.fn.tinymce_append = function(val) {
	var el_dom = document.getElementById(this.attr('id'));
	el_dom.contentDocument.body.innerHTML += val;
}

// Insert val at the caret position in the currently focused tinymce editor
function tinymce_insertAtCaret(val) {
	tinymce.activeEditor.execCommand('mceInsertContent', false, val);
}

function showModal(modalID) {
	// Show overlay
	$('#overlay').fadeIn();

	$modal = $('#' + modalID);

	// Set position of modal to be at center of screen
	$modal.center();

	// Show the new form
	$modal.fadeIn();
}

/*
	When file is selected in file input box, trigger fileselect
	event handler.
*/
$(document).on('change', '.btn-file :file', function(){
	var input = $(this);
	var numFiles = input.get(0).files ? input.get(0).files.length : 1;
	var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function (){
	$('.panel').click(function(e) {
		$child = $(this).find('a');

		// Prevent too much recursion error
		if (!$(e.target).is($child)) {
			$child.trigger(e.type);
		}
	});

	var init_tinymce_editor = function(sel, editorHeight) {
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
			selector: sel,
			height: editorHeight,		
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
	}

	init_tinymce_editor('textarea.richtext-lg', 350);
	init_tinymce_editor('textarea.richtext-md', 200);
	init_tinymce_editor('textarea.richtext-sm', 80);
	

	// Prepare overlay for modals
	$overlay = $('<div id="overlay"></div>');
	$('body').append($overlay);
	$overlay.hide();

	// Overlay click handler
	$('#overlay').click(function() {
		/*
			Hide each element with class="modal" that
			is currently visible, then hide the overlay.
		*/
		$('.modalForm:visible').each(function() {
			$(this).fadeOut();
			$('#overlay').fadeOut();
		})
	});

	/*
		When a file is selected in a file input box, fill
		input box with filename.
		If more than one file is selected, fill input box with
		the number of the files that were selected.
	*/
	$('.btn-file :file').on('fileselect', function(event, numFiles, label){
		// Get the selected file text
		var input = $(this).parents('.input-group').find(':text');
		var log = numFiles > 1 ? numFiles + ' files selected' : label;

		if (input.length) {
			input.val(log);
		}
		else{
			if (log){
				alert(log);
			}
		}
	});
});

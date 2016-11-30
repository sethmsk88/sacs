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

	var richtext_plugins = 'advlist anchor autolink charmap code contextmenu hr image imagetools link lists paste preview searchreplace spellchecker table textcolor wordcount';

	tinymce.init({
		selector: 'textarea.richtext-lg',
		height: 350,
		plugins: richtext_plugins,
		paste_data_images: true,
		elementpath: false,
		convert_urls: false
	});

	tinymce.init({
		selector: 'textarea.richtext-md',
		height: 200,
		plugins: richtext_plugins,
		paste_data_images: true,
		elementpath: false,
		convert_urls: false
	});

	tinymce.init({
		selector: 'textarea.richtext-sm',
		height: 80,
		plugins: richtext_plugins,
		paste_data_images: true,
		elementpath: false,
		convert_urls: false
	});

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

/**
 *	Center the calling element on the screen
 */
jQuery.fn.center = function() {
	this.css("position", "absolute");
	this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2 - 100) + $(window).scrollTop()) + "px");
	this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	return this;
}

/**
 *  Insert text at caret
 */
/*
function insertAtCaret(el_id, val) {
	var el_dom = document.getElementById(el_id);

	if (document.selection) {
		el_dom.focus();
		sel = document.selection.createRange();
		sel.text = val;
		return;
	}
	if (el_dom.selectionStart || el_dom.selectionStart == "0") {
		var t_start = el_dom.selectionStart;
		var t_end = el_dom.selectionEnd;
		var val_start = el_dom.value.substring(0, t_start);
		var val_end = el_dom.value.substring(t_end, el_dom.value.length);
		el_dom.value = val_start + val + val_end;
	} else {
		el_dom.value += val;
	}
}

// insert text into tinymce
//$('#descr').prev().find('iframe').contents().find('body').html('<p><b>Bold</b> Not Bold</p>');

jQuery.fn.insertAtCaret_tinymce = function(val) {
	var el_dom = document.getElementById(this.attr('id'));
	//el_dom.contentDocument.body.innerHTML = val;
	el_dom = el_dom.contentDocument.body;
	
	if (document.selection) {
		el_dom.focus();
		sel = document.selection.createRange();
		sel.text = val;
		return;
	}
	if (el_dom.selectionStart || el_dom.selectionStart == "0") {
		var t_start = el_dom.selectionStart;
		var t_end = el_dom.selectionEnd;
		var val_start = el_dom.value.substring(0, t_start);
		var val_end = el_dom.value.substring(t_end, el_dom.value.length);
		el_dom.value = val_start + val + val_end;
	} else {
		el_dom.value += val;
	}
	
}
*/

jQuery.fn.tinymce_append = function(val) {
	var el_dom = document.getElementById(this.attr('id'));
	el_dom.contentDocument.body.innerHTML += val;
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
		elementpath: false
	});

	tinymce.init({
		selector: 'textarea.richtext-md',
		height: 200,
		plugins: richtext_plugins,
		paste_data_images: true,
		elementpath: false
	});

	tinymce.init({
		selector: 'textarea.richtext-sm',
		height: 80,
		plugins: richtext_plugins,
		paste_data_images: true,
		elementpath: false
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

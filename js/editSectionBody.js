$(document).ready(function() {

	$('#editSectionBody-form').submit(function(e)) {
		e.preventDefault();

		// Save contents of tinymce rich textareas
		// This is required for richtext to be posted
		tinymce.triggerSave();

		$(this).submit();
	}
});

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
		paste_data_images: true
	});

	tinymce.init({
		selector: 'textarea.richtext-md',
		height: 200,
		plugins: richtext_plugins,
		paste_data_images: true
	});

	tinymce.init({
		selector: 'textarea.richtext-sm',
		height: 80,
		plugins: richtext_plugins,
		paste_data_images: true
	});
});

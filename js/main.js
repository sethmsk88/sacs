$(document).ready(function (){
	$('.panel').click(function(e) {
		$child = $(this).find('a');

		// Prevent too much recursion error
		if (!$(e.target).is($child)) {
			$child.trigger(e.type);
		}
	});

	tinymce.init({
		selector: 'textarea.richtext',
		height: 300,
		plugins: 'advlist anchor autolink charmap code contextmenu hr image imagetools link lists paste preview searchreplace spellchecker table textcolor wordcount',
		paste_data_images: true

	});
});

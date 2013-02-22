jQuery(document).ready(function($){

	$('#download-text').click(function(e){

		$.generateFile({
			filename	: 'export.txt',
			content		: $('div.export-area').text(),
			script		: templateDir.downloadfile
		});

		e.preventDefault();
	});

	$('#download-html').click(function(e){

		$.generateFile({
			filename	: 'content.html',
			content		: $('div.export-area').html(),
			script		: templateDir.downloadfile
		});

		e.preventDefault();
	});

});
$(document).ready(function() {

	$('.sortable').sortable({
		update : function(event, ui) {

			$.post(url_www + "Pub/Ajax/Edit/", {

				action : 'images_order',
				images : $(this).sortable('toArray')

			}, function(response) {
			});
		}
	}).disableSelection();

	if ($(".gallery_item").length > 0) {

		$('.gallery_item').photoSwipe({
			enableMouseWheel : true,
			captionAndToolbarHide : false,
			captionAndToolbarShowEmptyCaptions : false,
			captionAndToolbarFlipPosition : true,
			captionAndToolbarAutoHideDelay : 0,
			imageScaleMethod : 'fitNoUpscale'
		});

	}
});

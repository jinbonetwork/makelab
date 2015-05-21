var oneApp = oneApp || {};

(function($) {
	'use strict';

	var disable = false,
		classifierSection = {
		init: function() {
			$('.ttfmake-stage').on('click', '.ml-classify-section', function(e) {
				e.preventDefault();
				var $this = $(this),
					$section = $this.parents('.ttfmake-section'),
					view;

				jQuery.fancybox.open({
					
					content:$section.attr('id')
				});

				/*
				// Only proceed if duplication is not currently disabled
				if (false === disable) {
					// Activate the spinner
					$this.addClass('ttfmp-spinner');
					disable = true;

					wp.ajax.send( 'ttf_duplicate_section', {
						success: function(data) {
							if (data.result && 'success' === data.result && data.section) {
								$appendedSection = $(data.section);
								$appendedSection.appendTo($stage);

								// Init the views
								view = oneApp.initAllViews($appendedSection);

								// Scroll to the content
								oneApp.scrollToAddedView(view);

								// Register the section with the sortable order field
								oneApp.addOrderValue(view.model.get('id'), oneApp.cache.$sectionOrder);

								// Initiate sortables
								if ('text' === sectionType) {
									oneApp.initializeTextColumnSortables(view);
									duplicatorSection.initFrames(view);
								}
							} else {
								duplicatorSection.handleError(data, $this);
							}

							// Remove the spinner
							$this.removeClass('ttfmp-spinner');
							disable = false;
						},
						error: function(data) {
							duplicatorSection.handleError(data, $this);

							// Remove the spinner
							$this.removeClass('ttfmp-spinner');
							disable = false;
						},
						data: {
							nonce: ttfmpDuplicateSection.nonce,
							data: $('#post').serialize(),
							sectionType: sectionType,
							id: $el.attr('data-id')
						}
					});
				}
				*/
			});
		}
	};

	classifierSection.init();
})(jQuery);

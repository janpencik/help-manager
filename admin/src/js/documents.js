(function( $ ) {
	'use strict';

	$(document).ready(function() {

		// Add IDs to the sidebar navigation links
		$('.can-sort li.page_item').each(function() {
			const $this = $(this);
			$this.attr(
				'id',
				`page-${$this.attr('class').match(/page-item-([0-9]+)/)[1]}`
			);
		});

		// Responsive iframes
		function reframeIframes() {
			if( wphm_vars.format_iframes === 'true' ) {
				reframe('.wphm-docs-content iframe');
			}
		}
		reframeIframes();

		// Find all images wrapped in a link and activate gallery popup
		function initPopupGallery() {
			if( wphm_vars.image_popup === 'true' ) {
				if( $('.wphm-docs-content a img').length > 0 ) {
					var imagesWithLinks = $('.wphm-docs-content a img');
					imagesWithLinks.each(function() {
						var linkFilename = $(this).parent().attr('href').replace("-scaled", "").replace(/\.[^/.]+$/, "");
						var imgFilename = $(this).attr('src').replace(/\.[^/.]+$/, "");
						if ( imgFilename.includes( linkFilename ) ) {
							$(this).parent().addClass('wphm-gallery');
						}
					});
				}

				// RTL
				var langDir = 'ltr';
				if( $('body.rtl').length > 0 ) {
					langDir = 'rtl';
				} 

				$('.wphm-gallery').magnificPopup({
					gallery: {
						enabled: true,
						tCounter: '%curr%/%total%',
						langDir: langDir
					},
					type: 'image',
					image: {
						titleSrc: function(item) {
							
							// Try to find title
							var caption = item.el.parent().find('figcaption');
							if( caption ) {
								return caption.text();
							} 
							var title = item.el.find('img').attr('title');
							if( title ) {
								return title;
							}
							var alt = item.el.find('img').attr('alt');
							if( alt ) {
								return alt;
							}
							
							// Empty
							if( caption.length == 0 || title.length == 0 || alt.length == 0 ) {
								return '';
							}

						}
					},
					// overflowY: 'hidden'
				});
			}
		}
		initPopupGallery();

		// Fix captions in wp-block-image
		function fixFigcaption(el) {
			var imageWidth = el.parent().find('img').attr('width');
			el.css({
				'width': imageWidth + 'px',
			});
		}
		function fixDocumentFigcaptions() {
			if( $('.wphm-docs-content .wp-block-image figure.alignleft figcaption').length > 0 ) {
				$('.wphm-docs-content .wp-block-image figure.alignleft figcaption').each(function() {
					fixFigcaption( $(this) );
				});
			}
			if( $('.wphm-docs-content .wp-block-image figure.alignright figcaption').length > 0 ) {
				$('.wphm-docs-content .wp-block-image figure.alignright figcaption').each(function() {
					fixFigcaption( $(this) );
				});
			}
		}
		fixDocumentFigcaptions();

		// RTL
		var rtl = false;
		if( $('body.rtl').length > 0 ) {
			rtl = true;
		} 

		// Make documents in the sidebar navigation sortable (nested)
		$('.can-sort').nestedSortable({
			opacity: 0.8,
			forcePlaceholderSize: true,
			placeholder: 'wphm-sort-placeholder',
			handle: '.sort-handle',
			items: 'li',
			listType: 'ul',
			toleranceElement: '> span',
			maxLevels: 5,
			rtl: rtl,
			start(e, ui) {
				const item = $(ui.item);
				const placeholder = $('.wphm-sort-placeholder');
				let offset = -2;
				placeholder.height( item.height() + offset );
			},
			update(e, ui) {
				const request = $.post( ajaxurl, {
					action: 'wphm_docs_reorder',
					security: $('.can-sort').data('nonce'),
					order: $(this).sortable('toArray'),
				}, function(response) {
					if( response.success == true ) {
						$("#wphm-content-main").load(location.href + " #wphm-content-main>*");
					}
				});
			},
		});
		$(document).ajaxComplete(function () {
			if( $('audio').length > 0 || $('video').length > 0 ) {
				$('audio, video').mediaelementplayer();
			}
			reframeIframes();
			initPopupGallery();
			fixDocumentFigcaptions();
		});

		// Scroll to top
		if( $('.wphm-back-to-top').length > 0 ) {
			$('.wphm-back-to-top').on('click', function(e) {
				$(window).scrollTop({
					top: 0,
    				behavior: "smooth"
				});
			});
		}

		// Remove query args after dismissing the admin notice
		$(document).on('click', '.wphm-notice .notice-dismiss', function(e) {
			e.preventDefault();
			var close_url = $(this).parent().data('close');
			if( close_url ) {
				window.location.href = close_url;
			}
		});

	})

})( jQuery );

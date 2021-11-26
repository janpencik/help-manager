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
			reframe('.wphm-docs-content iframe');
		}
		reframeIframes();

		// Find all images wrapped in a link and activate gallery popup
		function initPopupGallery() {
			$('.wphm-docs-content a img').parent().magnificPopup({
				gallery: {
					enabled: true
				},
				type: 'image',
				image: {
					titleSrc: function(item) {
						
						// Try to find title
						var caption = item.el.parent().find('figcaption');
						if( caption.length > 0 ) {
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

					}
				}
			});
		}
		initPopupGallery();

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
		});

		// Clipboard.js
       	var copyLink = new ClipboardJS('.wphm-action-clipboard');
		copyLink.on('success', function(e) {

			e.trigger.querySelector('span:first-child').classList.remove('dashicons-admin-links');
			e.trigger.querySelector('span:first-child').classList.add('dashicons-clipboard');
			e.trigger.querySelector('span:last-child').innerHTML = 'Copied!';
			e.clearSelection();

			setTimeout( function() {
				e.trigger.querySelector('span:first-child').classList.remove('dashicons-clipboard');
				e.trigger.querySelector('span:first-child').classList.add('dashicons-admin-links');
				e.trigger.querySelector('span:last-child').innerHTML = 'Copy link';
			}, 2000);

		});

		// Trash document
		$('.wphm-action-trash').on('click', function(e) {
			if ( confirm('Are you sure you want to move this document to trash?') ) {
				const request = $.post( ajaxurl, {
					action: 'wphm_trash_document',
					security: $(this).data('nonce'),
					id: $(this).data('id')
				}, function(response) {
					if( response.success == true ) {
						window.location.href = response.data
					}
				});
			}
		});

		// Untrash document
		$('.wphm-action-untrash').on('click', function(e) {
			const request = $.post( ajaxurl, {
				action: 'wphm_untrash_document',
				security: $(this).data('nonce'),
				id: $(this).data('id')
			}, function(response) {
				if( response.success == true ) {
					window.location.href = response.data
				}
			});
		});

		// Allow to dismiss the admin notice after document is trashed
		$(document).on('click', '.wphm-notice .notice-dismiss', function(e) {
			e.preventDefault();
			var close_url = $(this).parent().data('close');
			if( close_url ) {
				window.location.href = close_url;
			}
		});

	})

})( jQuery );

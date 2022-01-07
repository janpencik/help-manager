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
		$(document).ajaxComplete( function( event, xhr, settings ) {
			if( settings.url.includes('help-manager-documents') ) {
				if( $('audio').length > 0 || $('video').length > 0 ) {
					$('audio, video').mediaelementplayer();
				}
				reframeIframes();
				initPopupGallery();
				fixDocumentFigcaptions();
				setAnchorsAndQuickNav();
			}
		});

		// Add anchors to headings
		function setAnchorsAndQuickNav() {
			const headings = new Array();
			if( $('.wphm-docs-content').length > 0 ) {
				$('.wphm-docs-content h1, .wphm-docs-content h2, .wphm-docs-content h3, .wphm-docs-content h4, .wphm-docs-content h5, .wphm-docs-content h6').each(function() {
					if( $(this).find('.wphm-docs-anchor').length === 0 ) {
						var html = $(this).html();
						var id = $(this).attr('id');
						var newHtml = html + '<a class="wphm-docs-anchor" href="#' + id + '">#</a>';
						$(this).html(newHtml);

						// push heading to array for quick navigation
						var headingClone = $(this).clone();
						headingClone.find('a').remove();
						var headingLevel = headingClone.prop('tagName');
						var headingCloneId = headingClone.attr('id');
						var headingCloneText = headingClone.text();
						headings.push([headingLevel, headingCloneId, headingCloneText]);
					}
				});
			}

			// Quick navigation
			if( $('.wphm-docs-content').length > 0 ) {
				if( headings.length > 0 ) {
					$('.wphm-quick-navigation').css('display', 'block');
					$('.wphm-quick-navigation ul li').remove();
					let headingLevelMax = headings[0][0].replace('H', '');
					headings.forEach(function(heading, index, array) {
						var headingLevel = heading[0].replace('H', '');
						$('.wphm-quick-navigation ul').append('<li data-level="' + headingLevel + '"><a href="#' + heading[1] + '">' + heading[2] + '</a></li>');
						if( headingLevel < headingLevelMax ) {
							headingLevelMax = headingLevel;
						}
					})
					// Set margin for lower heading levels
					$('.wphm-quick-navigation ul li').each(function() {
						var adjustStart = headingLevelMax * 8;
						var level = $(this).attr('data-level');
						$(this).css('margin-left', (level * 8 - adjustStart) + 'px');
					})
				}
			}

			// Quick navigation - calculate fixed position
			if( $('.wphm-quick-navigation').length > 0 ) {
				var adminBarHeight = $('#wpadminbar').outerHeight();
				var fixedNav = $('.wphm-quick-navigation-fixed');
				$(window).on('scroll', function() {
					if( $('.wphm-quick-navigation:visible').length > 0 ) {
						var contentDistanceFromTop = $('.wphm-content').offset().top - adminBarHeight - $(window).scrollTop();
						if( contentDistanceFromTop <= 0 ) {
							fixedNav.addClass('fixed');
							fixedNav.css('top', adminBarHeight + 8);
						} else {
							fixedNav.removeClass('fixed');
							fixedNav.css('top', 'auto');
						}
					}
				})
			}
		}
		setAnchorsAndQuickNav();

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
			if ( history.pushState ) {
				let searchParams = new URLSearchParams( window.location.search );
				searchParams.delete( 'wphm-notice' );
				let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
				window.history.replaceState( {path: newurl}, '', newurl );
			}
		});

	})

})( jQuery );

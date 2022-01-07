(function( $ ) {
	'use strict';

	$(document).ready(function() {

        // Get search parameters
        let searchParams = new URLSearchParams( window.location.search );

        // Remove admin notice parameter from WP referer
        function fixWpReferer() {
            if( searchParams.has('wphm-notice') ) {
                searchParams.delete( 'wphm-notice' );
                $("input[name=_wp_http_referer]").val( window.location.pathname + '?' + searchParams.toString() );
            }
        }

        // Remove query args after dismissing the admin notice
		$(document).on('click', '.wphm-notice .notice-dismiss', function(e) {
			e.preventDefault();
			if ( history.pushState ) {
				fixWpReferer();
				let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
				window.history.replaceState( { path: newurl }, '', newurl );
			}
		});

        // Update WP referer to not show admin notice after form update
        $('form[name=help-manager_options]').submit(function() {
            fixWpReferer();
        });

        // CodeMirror editor
        if( $('#help-manager-custom-css-custom-css').length > 0 ) {
            wp.codeEditor.initialize(
                $('#help-manager-custom-css-custom-css'), cm_settings
            );
        }

        // Export
        if( $('#wphm_docs_all').length > 0 ) {

            const inputs = $('input[name="wphm_docs[]"]');
            const inputs_count = inputs.length;
            const toggle = $('#wphm_docs_all');

            // Select/unselect all
            toggle.on( 'change', function() {
                if ( $(this).is(':checked') ) {
                    inputs.each( function( index ) {
                        $(this).prop( "checked", true );
                    })
                } else {
                    inputs.each( function( index ) {
                        $(this).prop( "checked", false );
                    })
                }
            });

            // Make change to main toggle if not all selected/all unselected
            inputs.on( 'change', function() {
                if( $('input[name="wphm_docs[]"]:checked').length > 0 && $('input[name="wphm_docs[]"]:checked').length < inputs_count ) {
                    toggle.prop( "checked", false );
                } else if( $('input[name="wphm_docs[]"]:checked').length == inputs_count ) {
                    toggle.prop( "checked", true );
                }
            });

        }

	});

})( jQuery );

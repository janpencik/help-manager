(function( $ ) {
	'use strict';

	$(document).ready(function() {

        // Remove query args after dismissing the admin notice
		$(document).on('click', '.wphm-notice .notice-dismiss', function(e) {
			e.preventDefault();
			var close_url = $(this).parent().data('close');
			if( close_url ) {
				window.location.href = close_url;
			}
		});

        // CodeMirror editor
        if( $('#wp-help-manager-custom-css-custom-css').length > 0 ) {
            wp.codeEditor.initialize(
                $('#wp-help-manager-custom-css-custom-css'), cm_settings
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

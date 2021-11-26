(function( $ ) {
	'use strict';

	$(document).ready(function() {

        // CodeMirror editor
        if( $('#wp-help-manager-custom-css-custom-css').length > 0 ) {
            wp.codeEditor.initialize(
                $('#wp-help-manager-custom-css-custom-css'), cm_settings
            );
        }

	});

})( jQuery );

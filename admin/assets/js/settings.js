!function(t){"use strict";t(document).ready(function(){if(t(document).on("click",".wphm-notice .notice-dismiss",function(c){c.preventDefault();c=t(this).parent().data("close");c&&(window.location.href=c)}),0<t("#wp-help-manager-custom-css-custom-css").length&&wp.codeEditor.initialize(t("#wp-help-manager-custom-css-custom-css"),cm_settings),0<t("#wphm_docs_all").length){const c=t('input[name="wphm_docs[]"]'),e=c.length,n=t("#wphm_docs_all");n.on("change",function(){t(this).is(":checked")?c.each(function(c){t(this).prop("checked",!0)}):c.each(function(c){t(this).prop("checked",!1)})}),c.on("change",function(){0<t('input[name="wphm_docs[]"]:checked').length&&t('input[name="wphm_docs[]"]:checked').length<e?n.prop("checked",!1):t('input[name="wphm_docs[]"]:checked').length==e&&n.prop("checked",!0)})}})}(jQuery);
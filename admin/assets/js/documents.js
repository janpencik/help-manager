!function(a){"use strict";a(document).ready(function(){function t(){reframe(".wphm-docs-content iframe")}function e(){a(".wphm-docs-content a img").parent().magnificPopup({gallery:{enabled:!0},type:"image",image:{titleSrc:function(t){var e=t.el.parent().find("figcaption");if(0<e.length)return e.text();e=t.el.find("img").attr("title");if(e)return e;t=t.el.find("img").attr("alt");return t||void 0}}})}a(".can-sort li.page_item").each(function(){const t=a(this);t.attr("id","page-"+t.attr("class").match(/page-item-([0-9]+)/)[1])}),t(),e(),a(".can-sort").nestedSortable({opacity:.8,forcePlaceholderSize:!0,placeholder:"wphm-sort-placeholder",handle:".sort-handle",items:"li",listType:"ul",toleranceElement:"> span",maxLevels:5,start(t,e){const i=a(e.item),n=a(".wphm-sort-placeholder");n.height(i.height()+-2)},update(t,e){a.post(ajaxurl,{action:"wphm_docs_reorder",security:a(".can-sort").data("nonce"),order:a(this).sortable("toArray")},function(t){1==t.success&&a("#wphm-content-main").load(location.href+" #wphm-content-main>*")})}}),a(document).ajaxComplete(function(){(0<a("audio").length||0<a("video").length)&&a("audio, video").mediaelementplayer(),t(),e()}),new ClipboardJS(".wphm-action-clipboard").on("success",function(t){t.trigger.querySelector("span:first-child").classList.remove("dashicons-admin-links"),t.trigger.querySelector("span:first-child").classList.add("dashicons-clipboard"),t.trigger.querySelector("span:last-child").innerHTML="Copied!",t.clearSelection(),setTimeout(function(){t.trigger.querySelector("span:first-child").classList.remove("dashicons-clipboard"),t.trigger.querySelector("span:first-child").classList.add("dashicons-admin-links"),t.trigger.querySelector("span:last-child").innerHTML="Copy link"},2e3)}),a(".wphm-action-trash").on("click",function(t){confirm("Are you sure you want to move this document to trash?")&&a.post(ajaxurl,{action:"wphm_trash_document",security:a(this).data("nonce"),id:a(this).data("id")},function(t){1==t.success&&(window.location.href=t.data)})}),a(".wphm-action-untrash").on("click",function(t){a.post(ajaxurl,{action:"wphm_untrash_document",security:a(this).data("nonce"),id:a(this).data("id")},function(t){1==t.success&&(window.location.href=t.data)})}),a(document).on("click",".wphm-notice .notice-dismiss",function(t){t.preventDefault();t=a(this).parent().data("close");t&&(window.location.href=t)})})}(jQuery);

var lang;

(function setLanguage() {
	lang = location.pathname.replace(/^\/([a-z]{2})\/.*$/, "$1");
	
})();


$(function() {
	
	// Activate tooltips for all elements with a title attribute
	$("[title]").tooltip(); 
});

if (typeof(core) === "undefined") {
	core = {};
}

core.displayFormErrors = function(messages)
{
	// clear all former errors:
	$(".form-element ul").remove();
	$(".form-element").removeClass('form-error');
	$("[title]").tooltip().tooltip('destroy').tooltip();
	
	var entries = {};
	function mapFormErrors(index, value, prefix) {
		if ($.isPlainObject(value) || $.isArray(value)) {
			prefix = (prefix ? prefix + '-' : '') + index;
			$.each(value, function(idx, val) { mapFormErrors(idx, val, prefix); });
		} else {
			if (!(prefix in entries)) {
				entries[prefix] = [];
			}
			entries[prefix].push(value);
		}
	};
	
	$.each(messages, function(index, value) { mapFormErrors(index, value, ""); });
	//c/onsole.debug(entries);
	if ($.isEmptyObject(entries)) return;

	$.each(entries, function(elementId, errors) {
		var html = '<ul>';
		$.each(errors, function(i, msg) {
			html += '<li>' + msg + '</li>';
		});
		html += '</ul>';
		var wrapper = $("#" + elementId + "-wrapper");
		
		wrapper/*.append(html)*/.addClass('form-error');
		var element = wrapper.is("[title]")
		            ? wrapper
		            : $("#" + elementId);
		
		element.tooltip({
			content: function() {
				return $(this).attr('title') + '<div class="form-element form-error">' + html + '</div>';
			}
		});
	});
	
};


/**
 * find elements in the surrounding DOM of the Object
 * stops either when an occurence has been found or when the Body-Tag is reached
 */
$.fn.findBrethren = function(selector) {
    var target = $(this);
    var b = true;
    var result = $();
    while (b) {
        b = false;
        if (target.get(0).nodeName != 'BODY') {
            result = target.find(selector);
            if (0 < result.length) {
                // found it
            }
            else {
                // go another node up
                target = target.parent();
                //c/onsole.log('target', target);
                if (0 < target.length) {
                        b = true;
                }
            }
        }
    }
    //c/onsole.log('findBrethren', $(this), result);
    return result;
}

/**
 * triggers the wait-Actions
 * wait Actions answer to 
 */

$.fn.waitAction = function(selector) {
    //c/onsole.log('waitAction', this);
    $(this).bind('wait.start', function(event) {
        var target = $(event.target);
        target.findBrethren(selector).show();
        return false;
    });
    $(this).bind('wait.stop', function(event) {
        var target = $(event.target);
        target.findBrethren(selector).hide();
        return false;
    });
    return this;
}


		


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
	console.debug(entries);
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
		

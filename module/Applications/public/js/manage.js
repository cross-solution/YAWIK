

(function($) 
{
	
	var activeActionBarId;
	var activeActionBar = {
			id: "",
			obj: null,
			clear: function() { this.id = ""; this.obj = null; },
			set: function(id, obj) { this.id = id; this.obj = obj; },
			slideUp: function() 
			{ 
				if (null === this.obj) return;
				
				var tr = $("#info-bar-" + this.id);
				this.obj.slideUp({
					complete: function()
					{
						tr.removeClass('active-row');
					}
				});
			}
	};
	
	function handleInfoBarClick (e) 
	{
		if (!$(this).hasClass("applications-list-row") || 'a' == e.target.tagName.toLowerCase()) {
			return;
		}
		e.stopPropagation();
		
		var id = $(this).attr('id').replace(/^info-bar-/, '');
		activeActionBar.slideUp();
		
		if (id == activeActionBar.id) {
			activeActionBar.clear();
			return;
		}
		
		var actionBar = $('#action-bar-' + id + ' > td > div');
		$(this).addClass('active-row');
		actionBar.slideDown();
		activeActionBar.set(id, actionBar);
	};
	
	$(function() {
		$("#applications-list tr").click(handleInfoBarClick);
		$(".application-actions-status").buttonset();
	});
	
})(jQuery);

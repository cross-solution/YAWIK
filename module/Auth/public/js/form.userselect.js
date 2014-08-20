
;(function($) {
	
	var searchQueryTimeout;
	
	function removeUser(event)
	{
		var divId = '#userselect-' + $(this).data('div-id');
		$(divId).remove();
		return false;
	}
	
	function searchUsers(event)
	{
		if (searchQueryTimeout) {
			clearTimeout(searchQueryTimeout);
		}
		var $query = $(this);
		searchQueryTimeout = setTimeout(function() {
			var query = $query.val();
			console.debug(query);
			$.post(basePath + '/' + lang + '/my/groups/search-users', {query: query})
			.done(searchUsersResult);
		}, 1000);
		//return false;
	}
	
	function findNextIndex()
	{
		var idx = 0;
		while ($('.userselect-wrapper .userselect-display #data-users-' + idx).length) {
			idx += 1;
		}
		return idx;
	}
	
	function searchUsersResult(data)
	{
		var $node = $('#search-users-result');
		$node.find('*').remove();
		
		$.each(data.users, function(idx, user) {
			var html  = $('#search-users-modal .modal-body span[data-template]').data('template');
			
			function replaceVars(str, data)
			{
				str = str.replace(/__user_id__/g, data.id)
			           .replace(/__name__/g, data.name)
			           .replace(/__company_name__/g, data.company)
			           .replace(/__company_position__/g, data.position);
			
				if (data.image) {
					str = str.replace(/##IMAGE:(.*?)##/, '$1')
						.replace(/__image_uri__/g, data.image)
						.replace(/##ICON:.*?##/, '');
				} else {
					str = str.replace(/##IMAGE:.*?##/, '')
							.replace(/__image_uri__/g, '')
							   .replace(/##ICON:(.*?)##/, '$1');
				}
				if (data.input) {
					str = str.replace(/__input__/g, data.input);
				}
				return str;
			}
			html = replaceVars(html, user);
			var $html = $(html.trim());
			$html.find('button').click(function() {
				var tmpl = $('.userselect .userselect-template').data('template');
				var input = $('.userselect span[data-template]').data('template');
				var $parent = $(this).parent();
				var data = $parent.data();
				data.input = input.replace(/__index__/g, findNextIndex());
				tmpl = replaceVars(tmpl, data);
				var $tmpl = $(tmpl.trim());
				$tmpl.find('button').click(removeUser);
				$tmpl.find('input').val(data.id);
				$('.userselect .userselect-wrapper').append($tmpl);
				$parent.addClass('panel-info');
				$(this).remove();
				return false;
			});
			if ($('#userselect-' + user.id).length) $html.addClass('panel-info').find('button').remove();
			$node.append($html);
			
		});
	}
	
	function init()
	{
		$('.userselect').each(function() {
			$(this).find('.userselect-wrapper .userselect-display button').click(removeUser);
			$(this).find('#userselect-add-button').click(function() {
				$('#search-users-modal').modal('show');
				return false;
			});
		});
		$('#search-users-modal')
		.on('show.bs.modal', function() { $(this).find('form input').trigger('keyup'); })
		.find('form input').keyup(searchUsers);
		$('#search-users-clear').click(function() {
			$('#search-users-modal form input').val('').keyup().focus();
			return false;
		});
		
	}
	
	$(function() { init(); });
})(jQuery);
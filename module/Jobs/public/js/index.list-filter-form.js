
;(function($) {
	
	var defaultParams = {};
	
	var onListFilterFormSubmit = function(event)
	{
		var $form = $(event.target);
		var url = $form.attr('action') + '?' + $form.serialize(); 
		var $container = $('#jobs-list-container');
		$container.load(url, function () { $container.pagination(); });
		return false;
	};
	
	var onRadioChange = function(event)
	{
		var $target  = $(this);
		console.debug($target, this);
		var idPrefix = $target.attr('id').replace(/-[^-]+$/, '');
		var value    = $target.attr('value');
		$('#' + idPrefix + '-value').val(value);
		$('#jobs-list-filter').submit();
	};
	
	var resetListFilter = function(event)
	{
		var $form = $('#jobs-list-filter');
		console.debug(defaultParams, $form.find('.btn-toolbar label'));
		$form.find('.btn-toolbar label').removeClass('active');
		$.each(defaultParams, function(idx, val) {
			var $elem = $form.find('[name="' + val.name + '"]');
			if ($elem.is(':radio')) {
				$elem.each(function() { 
					if ($(this).val() == val.value) {
						$(this).prop('checked', true);
						$(this).parent().addClass('active');
					}
				});
			} else {
				$elem.val(val.value);
			}
			
		});
		$form.submit();
		$('#params-search-wrapper .dropdown-toggle').dropdown('toggle');
		return false;
	};
	
	var initListFilter = function()
	{
		var $form = $('#jobs-list-filter');
		defaultParams = $form.serializeArray();
		$form.submit(onListFilterFormSubmit);
		$form.find('.btn-toolbar input:radio')
			.change(onRadioChange);
		$form.find('#jobs-list-filter-reset').click(resetListFilter);
	};
	
	$(function() { initListFilter();});
	
})(jQuery);


;(function($) {
	
	var defaultParams = {};
	
	var onListFilterFormSubmit = function(event)
	{
		$.loadingPopup.show();
		var $form = $(event.target);
		var url = $form.attr('action') + '?' + $form.serialize(); 
		var $container = $('#jobs-list-container');
		$container.load(url, function () { $container.pagination(); $.loadingPopup.hide(); });
		return false;
	};
	
	var onRadioButtonClick = function(event)
	{
		var $target  = $(event.target);
		var idPrefix = $target.attr('id').replace(/-[^-]+$/, '');
		var value    = $target.attr('value');
		$('#' + idPrefix + '-value').val(value);
		$('#jobs-list-filter').submit();
	};
	
	var resetListFilter = function(event)
	{
		var $form = $('#jobs-list-filter');
		$form.find('.btn-toolbar button').removeClass('active');
		$.each(defaultParams, function(idx, val) {
			var $elem = $form.find('[name="' + val.name + '"]').val(val.value);
			var $button = $('#params-' + val.name + '-' + val.value);
			if ($button.length) {
				$button.addClass('active');
			}
		});
		$form.submit();
		$('#params-search-wrapper .dropdown-toggle').dropdown('toggle');
		return false;
	};
	
	var initListFilter = function()
	{
		var $form = $('#jobs-list-filter')
		defaultParams = $form.serializeArray();
		$form.submit(onListFilterFormSubmit);
		$('#jobs-list-filter #params-by-group button, #jobs-list-filter #params-status-group button')
			.click(onRadioButtonClick);
		$('#jobs-list-filter-reset').click(resetListFilter);
	};
	
	$(function() { initListFilter();});
	
})(jQuery);

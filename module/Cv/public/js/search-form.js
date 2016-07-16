;(function ($) {
    var defaultParams = {};

    var onListFilterFormSubmit = function (event) {
        var $form = $(event.target);
        var url = $form.attr('action') + '?' + $form.serialize();
        var $container = $('#cv-list-container');
        $container.paginationContainer('load', url);
        return false;
    };

    var resetListFilter = function (event) {
        var $form = $('#cv-list-filter');
        console.debug(defaultParams, $form.find('.btn-toolbar label'));
        $form.find('.btn-toolbar label').removeClass('active');
        $.each(defaultParams, function (idx, val) {
            var $elem = $form.find('[name="' + val.name + '"]');
            if ($elem.is(':radio')) {
                $elem.each(function () {
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

    var initListFilter = function () {
        var $form = $('#cv-list-filter');
        defaultParams = $form.serializeArray();
        $form.submit(onListFilterFormSubmit);
        $form.find('#cv-list-filter-reset').click(resetListFilter);
    };

    $(function () {
        initListFilter();
    });

})(jQuery);
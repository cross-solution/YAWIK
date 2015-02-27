
(function($) {


    $(function() {
        $('.usersearchbar').usersearchbar();
    });

    $.fn.usersearchbar = function()
    {
        return this.each(function() {

            initialize($(this));
        });
    };

    function initialize($input)
    {
        console.debug('form.usersearchbar: initialize', $input.attr('id'));

        var usersearch = new Bloodhound({
            name: 'usersearchbar-' + $input.attr('id'),
            remote: basePath + '/user/search?q=%QUERY',
            valueKey: 'id',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        usersearch.initialize();

        var filterDisplayText = function(d) {
            return d.name + '&lt;' + d.email + '&gt;';
        };

        $('.usersearchbar').typeahead(
            {
                highlight: true,
                minLength: 2
            },
            {
                name: 'usersearchbar-' + $input.attr('id'),
                displayKey: filterDisplayText,
                source: usersearch.ttAdapter(),
                templates: {
                    suggestion: function(d) {
                        console.debug(d, d.name);
                        return '<p>' + d.name + '<br><small style="white-space:nowrap;">' + d.email + '</small></p>';
                    }

                }
            }
        ).on('typeahead:selected', function(e, d, n) {
                //var selectedValue = filterDisplayText(d);
                $input.trigger('yk.auth.usersearchbar.selected', {data: d});
                $input.val('');
            }).on('blur', function(e) {
                $input.val('');
            }).on('focus', function (e) {
                $(this).on('mouseup', function(e) {
                    e.preventDefault();
                    $(this).off('mouseup');
                }).select();
            });
    }

})(jQuery);

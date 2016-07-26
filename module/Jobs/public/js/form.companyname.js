/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
;
(function ($) {
    function initializeCompanyNameSelectField() {
        var selectedValue = null;

        var organizations = new Bloodhound({
            name: 'organizations',
            remote: basePath + '/' + lang + '/organizations/typeahead?q=%QUERY',
            valueKey: 'id',
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
            queryTokenizer: Bloodhound.tokenizers.whitespace
        });

        organizations.initialize();

        var filterDisplayText = function (d) {
            return d.name;
        };

        var $jobCompanyName = $('#jobCompanyName-company');

        $jobCompanyName
            .typeahead(
            {
                highlight: true,
                minLength: 2
            },
            {
                name: 'organizations',
                displayKey: filterDisplayText,
                source: organizations.ttAdapter(),
                templates: {
                    suggestion: function (d) {
                        return '<p>'
                            + d.name
                            + '<br><small style="white-space:nowrap;">'
                            + d.city
                            + ', '
                            + d.street
                            + ' '
                            + d.houseNumber
                            + '</small></p>';
                    }

                }
            }
        ).on('typeahead:selected', function (e, d, n) {
                selectedValue = filterDisplayText(d);
                $('#jobCompanyName-companyId').val(d.id);
            }
        ).on('blur', function (e) {
                if (selectedValue != $(this).val()) {
                    $(this).val('');
                    selectedValue = '';
                    $jobCompanyName.val('');
                }
            }
        ).on('focus', function (e) {
                $(this).on('mouseup', function (e) {
                    e.preventDefault();
                    $(this).off('mouseup');
                }).select();
            }
        );

        if ('' != $jobCompanyName.typeahead('val')) {
            selectedValue = $jobCompanyName.typeahead('val');
        }
    }

    $(function () {
        initializeCompanyNameSelectField();
    });

})(jQuery);
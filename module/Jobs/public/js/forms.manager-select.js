/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2017 CROSS Solution <http://cross-solution.de>
 */

/**
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    var $organizationSelect, $select;

    function loadOptions(ev)
    {
        $select[0].selectedIndex = -1;
        $select.trigger('change');

        var orgId = $organizationSelect.val();

        $.get('?ajax=jobs.manager-select&lang=' + lang + '&organization=' + orgId)
            .done(function(data) {
                if (data.managers && data.managers.length) {
                    $select.prop('disabled', false);
                    console.debug(data.managers);
                    var html = '';
                    var selected = true === ev.data.init ? $select.data('initialvalue').split(',') : [];
                    $.each(data.managers, function(i, manager) {
                        html += '<option value="'
                              + manager.id + '|' + manager.name + '|' + manager.email
                              + '"' + (-1 !== $.inArray(manager.id, selected) ? ' selected' : '') + '>'
                              + manager.name + '</option>';
                    });

                    console.debug(html);
                    $select.html(html);
                    $select.parent().parent().slideDown();
                } else {
                    $select.html('');
                    $select.prop('disabled', true);
                    $select.parent().parent().slideUp();
                }
            });
    }

    $(function() {
        $select = $('select.manager-select');
        $('<input type="hidden" name="' + $select.attr('name').slice(0,-2) + '" value="__empty__">').insertBefore($select);
        $organizationSelect = $select.parents('form').find('select[data-element="' + $select.data('organization-element') + '"]');

        console.debug('manager-select:init', $select.length, $organizationSelect.length);
        //@todo this does not work if editing a job. Maybe the initSelect event is too early?
        //$select.on('yk:forms:initSelect', {init:true}, loadOptions);
        loadOptions({data:{init:true}});
        $organizationSelect.on('change', {init:false}, loadOptions);

    });
})(jQuery); 
 

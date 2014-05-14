/* 
 * makes a checkbox in the header to a trigger for multiple checkboxes
 */

var setTableMultiCheckbox = function(target) {
    $(target).find('th').find(':checkbox').bind('click', function() {
        // which column
        var checked = $(this).prop('checked');
        var th = $(this).parents('th:first');
        var tr = th.parents('tr:first');
        var table = tr.parents('table:first');
        var tablebody = table.children('tbody');
        var row = -1;
        tr.children().each(function(i) {
            if (th.get(0) == $(this).get(0)) {
                row = i;
            }
        });
        if (0 <= row) {
            tablebody.children().each(function() {
                var rowElem = $(this);

                rowElem.children().each(function(i) {
                    if (i == row) {
                        $(this).find(':checkbox:first').prop('checked', checked);
                    }
                });
            });
        };
    })
};


(function ($) {
    $('document').ready(function() {
        
        setTableMultiCheckbox('div.pagination-container');
        $('.table-action').dropdown()

        
        $('div.pagination-container').on('ajax.ready', function() {
            setTableMultiCheckbox($(this));
            $('.table-action').dropdown()
        });

    });
    
})
(jQuery);



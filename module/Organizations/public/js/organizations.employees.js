
;(function($) {

    var $form;

    $(function() {
        $('.usersearchbar').on('selected.yk.auth.usersearchbar', userSelected);
        $('#employeesManagement').find('#employees-employees').find('.remove-employee').click(removeEmployee);
        $form = $('#employeesManagement');
        $form.on('yk.forms.done', handleFormResponse);

    });

    function handleFormResponse(e, data)
    {
        if (!data.isValid) {
            return;
        }

        console.debug(data);
    }

    function removeEmployee(e)
    {
        var fieldsetId = $(e.currentTarget).data('id');
        $('#' + fieldsetId).remove();
    }

    function userSelected(e, d)
    {
        console.debug('userSelected: ', d);

        var $fs   = $form.find('#employees-employees .fieldset-content');
        var tmpl  = $fs.find('span[data-template]').data('template');

        var index = 0;

        while($fs.find('fieldset#employees-employees-'+index).length) {
            index += 1;
        }

        var html  = tmpl.replace(/__index__/g, index)
                        .replace(/__userId__/g, d.data.id)
                        .replace(/__userName__/g, d.data.name)
                        .replace(/__userEmail__/g, d.data.email);

        $fs.append(html);
        $fs.find('#employees-employees-' + index).find('.remove-employee').click(removeEmployee);
        //$form.submit();
    }

})(jQuery);
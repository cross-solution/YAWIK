
;(function($, location) {

    var $form;

    $(function() {
        $form = $('#employeesManagement');
        $form.find('#employees-employees').find('.remove-employee').click(removeEmployee);
        $form.on('yk.forms.done', handleFormResponse);
        $('.invite-employee-bar').on('done.yk.organizations.invite-employee-bar', onEmployeeInvited);
        console.debug($form);
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

    function onEmployeeInvited(e, d)
    {
        console.debug($form);
        var $fs   = $form.find('#employees-employees .fieldset-content');
        var tmpl  = $fs.find('span[data-template]').data('template');

        var index = 0;

        while($fs.find('fieldset#employees-employees-'+index).length) {
            index += 1;
        }

        var html  = tmpl.replace(/__index__/g, index)
                        .replace(/__userId__/g, d.userId)
                        .replace(/__userName__/g, d.userName)
                        .replace(/__userEmail__/g, d.userEmail);

        $fs.append(html);
        $fs.find('#employees-employees-' + index).find('.remove-employee').click(removeEmployee);
        //$form.submit();
    }

})(jQuery, document.location);
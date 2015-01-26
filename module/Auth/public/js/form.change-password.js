;(function ($) {
    var $form = $('#user-password-form');
    $form.on('yk.forms.done', function (event, object) {
        event.stopPropagation();
        //new PNotify({
        //    text: object.data.text,
        //    type: object.data.status
        //});
    });
})(jQuery);
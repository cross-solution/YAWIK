(function($) {

var semaphor_formvalidation = false;
// this is for initial triggering after the site is loaded
$(document).ready(function() {
    $('form').bind('submit', function(event) {
        //var form = $(event.target).parents('form').eq(0);
        if (!semaphor_formvalidation) {
            semaphor_formvalidation = true;
            $('.error').empty().removeClass('.error');
            $('.input-error').removeClass('input-error');
            form = $(event.target);
            var action = form.attr('action');
            if (typeof action == 'undefined' || action.length == 0) {
                // there is no action, this form is submitted by other means
                return true;
            }
            // collect all inputs into erg
            var erg = {};
            form.find(":input").each(function () {
                //console.log($(this).prop('name'), $(this).attr('type'));
                if ($(this).attr('type') == 'checkbox') {
                    erg[$(this).prop('name')] = $(this).prop('checked');
                }
                else {
                    erg[$(this).prop('name')] = $(this).val();
                }
            });
            console.log(erg);

            // set process-icon
            var submits = form.find('button[type=submit]');
            submits.find('.default').addClass('yk-hidden');
            submits.find('.processing').removeClass('yk-hidden');
            $.post(action,erg,function(data) {
                // remove process-icon
                submits.find('.default').removeClass('yk-hidden');
                submits.find('.processing').addClass('yk-hidden');
                //console.log(typeof data, typeof data['ok'] , data['ok']);
                 if (typeof data != 'object' || typeof data['ok'] == 'undefined' || data['ok'] == true) {
                     // handle diffent cases, return object is
                     // - not a JSON
                     // - has no ok-flag
                     // - ok-flag is true
                    form.unbind('submit'); 
                    form.submit();
                    return true;
                }
                semaphor_formvalidation = false;
                var messages = data['messages'] == 'undefined' || data['messages'];
                validationFormRek(form, messages)
                // scrollTo still doesn't work, but we shouldn't be bothered
                //$('.error').parents('fieldset').eq(0).scrollTo({duration:400, offsetTop : '250'});
            }).fail(function() {
                form.submit();
            });
        }
        else {
            // Semaphore activ
        }
        return false;
    })
});

function validationFormRek(target, messages,prefix, pred_prefix) {
    // unravel the objects down to it's container-IDs
    if (typeof pred_prefix == 'undefined') {
        pred_prefix = '';
    }
    if (typeof prefix == 'undefined') {
        prefix = '';
    }
    if (typeof messages == 'object') {
        for (key in messages) {
            var prefix_new = ((prefix.length == 0)?'':prefix + '-') + key;
            validationFormRek(target, messages[key],prefix_new, prefix);
        }
    }
    else {
        var error_box = $('#' + pred_prefix + '-errors');
        var parent_of_error_box = $('#' + pred_prefix + '-span');
        if (parent_of_error_box.length == 0) {
            parent_of_error_box = error_box.parent();
        }
        parent_of_error_box.addClass('input-error');
        error_box.addClass('error');
        error_box.append(messages + '<br />');
    }
}

})(jQuery);


/**
 * Created by weitz on 14.11.14.
 */


;(function($) {

    $(function() {
        // sends the testmail in the application, in the case, that the application is on a job with no recruiter (ATS-enabled)
        $('#testmail-application-test').click(function(event) {
            //c/onsole.log(event.delegateTarget);
            try {
                var href = $(event.delegateTarget).attr('href');
                //c/onsole.log('application.form.js', href);
                $.get(href, function(data) {
                    $(document).trigger('ajax.ready', {'data': data});
                });
            }
            catch (e) {
                console.log('exception in application.form.js', e);
            }
            return false;
        });
    });

    $(document).ready(function () {
        $('form').on('yk.forms.done', function(event, data) {
            if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {
                if (typeof data['data']['isApplicationValid'] != 'undefined' && data['data']['isApplicationValid'] == true) {
                    $('#application_incomplete').hide();
                    $('#send-application').removeClass('hidden');
                    $('#testmail-application-test').attr('disabled', false);
                    $('#testmail-application-apply').attr('disabled', false);

                }
                else {
                    $('#application_incomplete').show();
                    $('#send-application').addClass('hidden');
                    $('#testmail-application-test').attr('disabled', true);
                    $('#testmail-application-apply').attr('disabled', true);
                }
            }
        });
    });

})(jQuery)



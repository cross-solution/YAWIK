/**
 * Created by weitz on 14.11.14.
 */


;(function($) {

    $(document).ready(function () {
        $('form').on('yk.forms.done', function(event, data) {
            if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {
                if (typeof data['data']['isApplicationValid'] != 'undefined' && data['data']['isApplicationValid'] == true) {
                    $('#application_incomplete').hide();
                    $('#send-application').removeClass('hidden');

                }
                else {
                    $('#application_incomplete').show();
                    $('#send-application').addClass('hidden');
                }
            }

        });
    });

})(jQuery)



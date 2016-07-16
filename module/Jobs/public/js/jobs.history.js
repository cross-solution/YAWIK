/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */
/**
 * Created by cbleek on 12.04.16.
 */

(function($) {
    var loadingText;
    $(function() {
        var $modal = $('#job-application-history');
        loadingText = $modal.find('.modal-header h4').html();

        $modal.on('hidden.bs.modal', function() {
            $modal.find('.modal-body').empty();
            $modal.find('.modal-header h4').html(loadingText);
            $modal.removeData('bs.modal');

        });
    });
})(jQuery);
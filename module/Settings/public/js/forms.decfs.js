/**
 * YAWIK
 * @overview Handles DisableElementsCapableFormSettings form element
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @copyright 2013-2014 Cross Solution <http://cross-solution.de>
 * @license MIT
 */

;(function($) {

    function toggleList(e)
    {
        var $box = $(e.currentTarget);
        var $list = e.data.ul;

        if ($box.prop('checked')) {
            $list.animate({height: 'toggle'});
        } else {
            $list.animate({height: 'toggle'});
        }
    }

    function init($ul)
    {
        $ul.find('.disable-elements-toggle').each(function() {
            var $box  = $(this);
            var $list = $box.closest('li').find('ul');
            if (!$box.prop('checked')) {
                $list.hide();
            }

            $box.click({ ul: $list }, toggleList);
        });
    }

    $(function() {
        $('.disable-elements-list').each(function() {
            init($(this));
        });
    });

})(jQuery);

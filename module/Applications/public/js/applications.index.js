
;(function($) {

    $(function() {
        $(document).on('click', '#application-multiple-move', function (event) {
            event.preventDefault();
            $(event.target).trigger('wait.stop');
            var ids = getTableMultiCheckbox(event.target);
            if (ids.length == 0) {
                return;
            }
            var $modal = $('#application-multiple-move-modal'),
                $form = $modal.find('form');

            $form.find('input[type=hidden]').remove();
            $.each(ids, function (index, id){
                $form.append('<input type="hidden" name="ids[]" value="' + id + '" >');
            });
            $modal.modal();
        });
    });
	
})(jQuery);

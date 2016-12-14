/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2016 CROSS Solution <http://cross-solution.de>
 */

/**
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    function TreeManagementForm($form)
    {
        this.$form = $form;
        this.nextIndex = 1;

        this.init();
    }

    $.extend(TreeManagementForm.prototype, {
        init: function()
        {
            console.debug('yktm:init');
            this.$form.on('click', 'button.yktm-add', $.proxy(this.onAddItem, this));
            this.$form.on('click', 'button.yktm-remove', $.proxy(this.onRemoveItem, this));
            this.$form.on('yk:forms:success.yktm', $.proxy(this.onSuccess, this));
            this.nextIndex = this.$form.find('.panel').length + 1;
            this._render();
        },

        onAddItem: function(event)
        {
            var $button = $(event.currentTarget);
            var current = $button.data('yktm-current');
            console.debug('yktm:click', current, $button);
            this.addItem(current);
        },

        onRemoveItem: function(event)
        {
            var $button = $(event.currentTarget);
            var current = $button.data('yktm-current');

            this.removeItem(current);
        },

        addItem: function(current)
        {
            var isRoot = 1 == current;
            var html    = this.$form.find('span[data-template]').data('template');
            var $target = isRoot ? this.$form.find('.yktm-management-fieldset') : this.$form.find('#yktm-' + current).find('> .panel-body');
            var priority = $target.find('> .panel').length + 1;
            targetCurrent = $target.data('current') ? $target.data('current') : 1;
            $target.data('current', targetCurrent + 1);
            current += '-' + targetCurrent;
            html = html.replace(/__index__/g, this.nextIndex++).replace(/__current__/g, current);
            var $html = $(html);
            $html.find('.yktm-current').val(current);
            $html.find('.yktm-priority').val(priority);
            $target.append($html);
        },

        removeItem: function(current)
        {
            var $target = this.$form.find('#yktm-' + current);
            var $id     = $target.find('.yktm-id');
            if ('' == $id.val()) {
                $target.remove();
            } else {
                $target.find('.yktm-do').val('remove');
                $target.hide();
            }
        },

        onSuccess: function(event, result)
        {
            if (!result.data.valid) {
                return;
            }

            var $html = $(result.data.content);
            var $formHtml = $html.find('#yktm-form-content').data('template');
            this.$form.find('.yktm-management-fieldset').remove();
            this.$form.prepend($formHtml);
            this._render();
        },

        _render: function()
        {
            var that = this;

            this.$form.find('.panel').each(function() {
                var $panel = $(this);
                var id = $panel.attr('id');
                var parts = id.split('-');
                console.log('yktm:id = ' + id + '; parts: ' , parts);
                if (3 < parts.length) {
                    parts.pop();
                    var parentId = parts.join('-');
                    console.log('yktm:rearrange; parentId: ' + parentId);
                    $panel.detach();
                    that.$form.find('#' + parentId).find('.panel-body').append($panel);
                }
            });
        }



    });

    $.fn.treemanagementform = function()
    {
       return this.each(function() {
           new TreeManagementForm($(this));
       })
    };

    $(function() {
        $('form.yk-tree-management-form').treemanagementform ();
    });

})(jQuery); 
 

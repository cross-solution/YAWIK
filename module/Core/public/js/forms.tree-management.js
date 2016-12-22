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
(function ($, Sortable) {

    function createSortable($target)
    {
        Sortable($target.get(),
                        {
                            items: '.panel',
                            forcePlaceholderSize: true,
                            placeholder: '<div class="panel panel-warning"></div>'
                        }
        );
    }

    function TreeManagementForm($form)
    {
        this.$form = $form;
        this.nextIndex = 1;

        this.init();
    }

    $.extend(TreeManagementForm.prototype, {
        init: function()
        {
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
            var $target = isRoot ? this.$form.find('.yktm-management-fieldset') : this.$form.find('.yktm-item-' + current).find('> .panel-body');

            targetCurrent = $target.data('current') ? $target.data('current') : 1;
            $target.data('current', targetCurrent + 1);
            current += '-' + targetCurrent;
            html = html.replace(/__index__/g, this.nextIndex++).replace(/__current__/g, current);
            var $html = $(html);
            $html.find('.yktm-current').val(current);
            $target.append($html);
            //$html.sortable({group: $html.attr('id'), draggable: '.panel', onEnd: $.proxy(this._onSortStop, this)});
            this._createSortable($html);
            Sortable($target.get());
            this._setPriorities($target);
        },

        removeItem: function(current)
        {
            var $target = this.$form.find('.yktm-item-' + current);
            var $parent = $target.parent();
            var $id     = $target.find('.yktm-id');
            if ('' == $id.val()) {
                $target.remove();
            } else {
                $target.find('.yktm-do').val('remove');
                $target.hide();
            }
            this._setPriorities($parent);
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
                console.debug($panel);
                var id = $panel.attr('class').split(' ').pop();
                var parts = id.split('-');
                //$panel.sortable({draggable: '.panel', onEnd: $.proxy(that._onSortStop, that)});
                if (4 < parts.length) {
                    parts.pop();
                    var parentId = parts.join('-');
                    console.debug(parentId);
                    $panel.detach();
                    that.$form.find('.' + parentId).find('> .panel-body').append($panel);
                }
            });

            var $main = this.$form.find('.yktm-management-fieldset');
            this._createSortable($main);
            this._setTargetCurrent($main);
            this._setPriorities($main);
            $main.find('.panel').each(function() {
               var $panel = $(this);
               var $panelBody = $panel.children('.panel-body');
                that._setPriorities($panelBody);
                that._createSortable($panelBody);
            });
//            $main.sortable( {
//                draggable: '.panel',
//                onEnd: $.proxy(this._onSortStop, this)
//            });

        },

        _setTargetCurrent: function($target)
        {
            var that = this;
            var $children = $target.find('> .panel > .panel-body');
            $target.data('current', $children.length + 1);

            $children.each(function() { that._setTargetCurrent($(this)); });
        },

        _setPriorities: function($target)
        {
            $target.find('> .panel > .panel-body > .row > .yktm-priority').each(function(i) {
                $(this).val(i + 1);
            });
        },

        _createSortable: function($target)
        {
            console.debug('createSortable: ' , $target.get());
            Sortable($target.get(),
                {
                    items: '.panel',
                    handle: '.yktm-sort-handle',
                    forcePlaceholderSize: true,
                    placeholder: '<div class="panel panel-warning"></div>'
                }
            );

            $target.on("sortstop", $.proxy(this._onSortStop, this));
        },

        _onSortStop: function(event)
        {
            this._setPriorities($(event.currentTarget));
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

})(jQuery, sortable);
 

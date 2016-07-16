/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * Handles the invite employee bar form element
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    function InviteEmployeeBar(node)
    {
        this.$container = $(node);
        this.$input     = this.$container.find('.ieb-input');
        this.$button    = this.$container.find('.ieb-button');
        this.$error     = this.$container.find('.ieb-error');

        this.init();

    }

    $.extend(InviteEmployeeBar.prototype,
    {
        init: function()
        {
            this.$error.hide();

            var selfReference = { instance: this };
            this.$button.click(selfReference, this.eventHandler)
                .find('.ieb-button-process').hide();
            this.$input.on("keydown keyup keypress", selfReference, this.eventHandler);
        },

        eventHandler: function(e)
        {
            if ("click" == e.type
                || ("keyup" == e.type && 13 == e.which)
            ) {

                /*
                 * this is now the element which triggered the event. so we need to get
                 * the class instance from the event data
                 */
                e.data.instance.process();
            }

            // prevent enter key in input from submitting the surrounding form
            return 13 != e.which;
        },

        process: function()
        {
            this.$error.slideUp();
            var email = this.$input.val();
            if ('' == email) {
                this.$input.blur();
                this.$button.blur();
                return;
            }

            this.showProcess();

            var self = this;

            $.get(this.$container.data('href'), { email: email })
             .done(function(data) {
                if (data.ok) {
                    self.done(data.result);
                } else {
                    self.fail(data.message ? data.message : 'Something went wrong.');
                }
             })
             .fail(function(jqXHR, status, message) {
                self.fail(message);
             })

        },

        done: function(result)
        {
            this.hideProcess();
            this.$container.trigger('done.yk.organizations.invite-employee-bar', result);
        },

        fail: function(message)
        {
            this.hideProcess();
            this.$error.find('p').html(message);
            this.$error.slideDown();
        },

        showProcess: function()
        {
            this.$button.find('.ieb-button-process').show();
        },

        hideProcess: function()
        {
            this.$button.find('.ieb-button-process').hide();
        }
    });

    $.fn.inviteEmployeeBar = function()
    {
        return this.each(function() {
            new InviteEmployeeBar(this);
        });
    };

    $(function() {
       $('.invite-employee-bar:not([data-autoinit="false"])').inviteEmployeeBar();
    });

})(jQuery); 
 

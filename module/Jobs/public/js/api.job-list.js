var Yawik = (function ($) {
    'use strict'

    var config = {
        container: '#yawik',
        jobList: []
    };

    function init() {
        var $container = $(config.container);

        $.each(config.jobList, function(i)
        {
            var ul = $('<ul/>')
                .addClass('ui-menu')
                .appendTo($container);

            var li = $('<li/>')
                .addClass('ui-menu-item')
                .attr('role', 'menuitem')
                .appendTo(ul);

            var a = $('<a/>')
                .addClass('ui-all')
                .text(countries[i])
                .appendTo(li);
        });
    }

    return {config: config, init: init};
})(jQuery);

Yawik.config.jobList = [{"title":"Poszukiwany wymiatacz PHP","location":"Krakow","organization":{"name":"F.H.U. Studioars"},"template_values":{"requirements":"\u003Cp\u003EPHP OOP\u003C\/p\u003E","qualification":"\u003Cp\u003ESOLID\u003C\/p\u003E","benefits":"\u003Cp\u003EMultisport\u003C\/p\u003E"}}];

$(function() {
    Yawik.config.container = '#yawik';
    Yawik.init();
});


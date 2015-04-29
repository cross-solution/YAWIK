(function ($) {

    initLanguageSwitcher = function () {
        $("#language-switcher button").click(function (e) {
            var switchToLang = '/' + $(this).attr("id").replace(/^language-switcher-/, "");

            if (lang != switchToLang) {
                var langRegex = new RegExp('/' + lang + '($|\/)');

                var newHref = location.protocol
                    + "//" + location.host
                    + location.pathname.replace(langRegex, switchToLang + '$1')
                    + location.search;
                //console.log(newHref);
                location.href = newHref;
            }
        });
    };
    initPnotify = function () {
        PNotify.prototype.options.styling = "fontawesome";
    };

    $(function () {
        initLanguageSwitcher();
        initPnotify();
    });

    $(function() {
        // take the normal notification and redirect them
        var reDirectNotifications = function() {
            $('.alert').each(function() {
                // only take those alerts that have a close button
                // there are other elements with an alert-class, which are permanent
                console.log('notify', $(this));
                if (0 < $(this).find('a.close').size() || 0 < $(this).find('button.close').size()) {
                    var type = 'success';
                    if ($(this).hasClass('alert-danger')) {
                        type = 'error';
                    }
                    if ($(this).hasClass('alert-info')) {
                        type = 'info';
                    }
                    // take out all operational chars, this takes also
                    // $(this).children().empty();
                    $message = $(this).children('.notification-content').eq(0);
                    if (typeof $message != undefined) {
                        // @TODO: change this so you can use tags inside the Message
                        $message = $message.text();
                    }
                    console.log('message', $message );

                    var targetId = $(this).attr('target');
                    if (typeof targetId != 'undefined') {
                        console.log('targetId', targetId);
                        console.log('target', $('#' + targetId));
                        target = $('#' + targetId);
                    }

                    if (typeof target == 'undefined') {
                        new PNotify({
                            // trim startinn and trailing whitespaces
                            'text': $message.replace(/^\s+|\s+$/gm,''),
                            'type': type
                        });
                    }
                    else {
                        target.append($message);
                    }
                    $(this).remove();
                }
            })
        }
        reDirectNotifications();
        $(document).on('ajax.ready', function(event, data) {
            //console.log('global.ajax.ready', event, data);
            reDirectNotifications();
            if (typeof(data) !== 'undefined' && typeof(data.data.notifications) !== 'undefined') {
                for (var notificationKey in data.data.notifications) {
                    if (data.data.notifications.hasOwnProperty(notificationKey)) {
                        var notification = data.data.notifications[notificationKey];
                        //console.log(notification);
                        new PNotify({
                            'text': notification.text,
                            'type': notification.status
                        });
                    }
                }
            }
        });
    });

})(jQuery);
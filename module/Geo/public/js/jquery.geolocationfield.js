(function($) {
var geolocation_appendTrigger = function (target) {

    var geolocation = new Bloodhound({
        datumTokenizer: function (d) {
            return $.parseJSON(d);
        },
        limit: 8,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: basePath + '/' + lang + '/geo?q=%QUERY'
    });

    geolocation.initialize();

    target.find('.geolocation').typeahead({
        hint: true,
        highlight: true,
        minLength: 2
    }, {
        name: 'geoLocation',
        displayKey: function (obj) {
            return obj;
        },
        source: geolocation.ttAdapter()
    });

};

// this is for initial triggering after the site is loaded
$(document).ready(function() {
    $.event.trigger('content.loaded', {'target': $('body')});
});

// this is for the special trigger 
$(document).on('content.loaded',function(event, data) {
    //console.log('react', data['target']);
    geolocation_appendTrigger(data['target']);
});

})(jQuery);
(function($) {
    var geolocation_appendTrigger = function (target) {

        var geolocation = new Bloodhound({
            datumTokenizer: function (d) {
                return $.parseJSON(d);
            },
            limit: 8,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: basePath + '/' + lang + '/geo/' + plugin + '?q=%QUERY'
        });

        geolocation.initialize();

        var filterDisplayText = function(data) {
            var d = $.fn.photon.getOptionData(data);


            var r = '';

            switch(d.osm_key){
                case "highway":
                    r += "<i class=\"fa fa-road\"></i> "  + d.name + "<br><small>" + d.street + "<br>"  + d.postcode + " " + d.city + "</small>";
                    break;
                case "building":
                    r += "<i class=\"fa fa-building-o\"></i> " + d.name + "<br><small>" + d.street + "<br>"  +d.postcode + " " + d.city + "</small>";
                    break;
                case "shop":
                    r += "<i class=\"fa fa-shopping-cart\"></i> " + d.name + "<br><small>" + d.street + "<br>"  +d.postcode + " " + d.city + "</small>";
                    break;
                case "landuse":
                    r += "<i class=\"fa fa-industry\"></i> " + d.name + "<br><small>" + d.street + "<br>"  +d.postcode + " " + d.city + "</small>";
                    break;
                case "boundary":
                    r += "<i class=\"fa fa-flag\"></i> " + d.name + "<br><small>(" + d.area + " " + d.country + ")</small>";
                    break;
                default:
                    for (var key in d) {
                        r += key + ": " + d[key] + '; ';
                    }
                    break;
            }

            return r;

        };

        target.find('.geolocation').typeahead({
            hint: true,
            highlight: true,
            minLength: 2
        }, {
            name: 'geoLocation',
            displayKey: 'value',
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'no results found',
                    '</div>'
                ].join('\n'),
                    suggestion: filterDisplayText

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

    $.fn.photon = {};

    $.fn.photon.orderElements = {};

    $.fn.photon.getOptionData = function(text)
    {
        var textArr = text.split('|');
        console.log('Locations', textArr);
        return {
            name: textArr[0],
            postcode: textArr[1],
            city: textArr[2],
            street: textArr[3],
            area: textArr[4],
            country: textArr[5],
            point: textArr[6],
            osm_key: textArr[7],
            osm_value: textArr[8],
            osm_id: textArr[9]
        };
    };

})(jQuery);
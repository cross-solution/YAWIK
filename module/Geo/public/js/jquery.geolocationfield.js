(function($) {
    var geolocation_appendTrigger = function (target) {

        var geolocation = new Bloodhound({
            datumTokenizer: function (d) {
                return $.parseJSON(d);
            },
            limit: 10,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote:{
                url: basePath + '/' + lang + '/geo/' + plugin + '?q=%QUERY',
                rateLimitWait: 400,
                rateLimitBy: 'debounce',
                filter: function(response) {
                    return response;
                }
            },
            source:{

            }
        });

        geolocation.initialize();

        var filterDisplayText = function(data) {

            var r = '';

            switch(data.osm_key){
                case "highway":
                    r += "<i class=\"fa fa-road\"></i> "  + data.name + "<br><small>"  + data.postcode + " " + data.city + "</small>";
                    break;
                case "place":
                    r += "<i class=\"fa fa-map-pin\"></i> "  + data.name + "<br><small>"  + (data.state + " " + data.country) + "</small>";
                    break;
                case "building":
                    r += "<i class=\"fa fa-home\"></i> " + data.name + "<br><small>" + data.street + "<br>"  +data.postcode + " " + data.city + "</small>";
                    break;
                case "office":
                    r += "<i class=\"fa fa-industry\"></i> " + data.name + "<br><small>" + data.street + "<br>"  +data.postcode + " " + data.city + "</small>";
                    break;
                case "shop":
                    r += "<i class=\"fa fa-shopping-cart\"></i> " + data.name + "<br><small>" + data.street + "<br>"  +data.postcode + " " + data.city + "</small>";
                    break;
                case "landuse":
                    r += "<i class=\"fa fa-industry\"></i> " + data.name + "<br><small>" + data.street + "<br>"  +data.postcode + " " + data.city + "</small>";
                    break;
                case "boundary":
                    r += "<i class=\"fa fa-flag\"></i> " + data.name + "<br><small>(" + data.state + " " + data.country + ")</small>";
                    break;
                default:
                    for (var key in data) {
                        r += key + ": " + data[key] + '; ';
                    }
                    break;
            }

            return r;

        };

        var displayText = function(data) {
            var r="";

            if(data.postcode) {
                r += data.postcode + ' ';
            }
            if(data.city) {
                r += data.city + ' ';
            }
            if(data.street) {
                r += ', ' + data.city;
            }
            if (r =="") {
                r = data.name;
            }
            console.log('displayText', data.name);

            return r;
        };

        target.find('.geolocation').typeahead({
            hint: true,
            highlight: true,
            minLength: 2
        }, {
            name: 'geoLocation',
            displayKey: displayText,
            templates: {
                empty: [
                    '<div class="empty-message">',
                    'no results found',
                    '</div>'
                ].join('\n'),
                suggestion: filterDisplayText

            },
            source: geolocation.ttAdapter()
        })
        /**
         * @todo: make passing of coordinates failsafe
         */
            .on('typeahead:selected', function($e, data){
                $('#coordinates').val(data.coordinates)
            })
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
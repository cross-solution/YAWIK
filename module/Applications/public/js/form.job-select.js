/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2017 CROSS Solution <http://cross-solution.de>
 */

/**
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {



    $(function() {
        $('select[data-element="job-select"]').each(function() {

            var $select = $(this);
            var data = $select.data();
            var options = {
                allowClear: true,
                theme:"bootstrap",
                placeholder: { id: "0", text: data.placeholder },
                //templateResult: displayResult,
                //templateSelection: displaySelection


                ajax: {
                    url: basePath + '/?ajax=applications.job-select',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.debug(params);
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;

                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.count
                            }
                        };
                    },
                    cache: true
                }
            };

            $select.select2(options);
        });
    });

})(jQuery); 
 

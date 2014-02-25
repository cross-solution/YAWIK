/*
 * Extension of Bootstrap Modal
 * Allows reloading of modal content.
 * 
 * To activate you need to pass the "reloadable" option which expects following values:
 * - true: enable reloading. Upon each toggle the toggle's href will replace the entire modal content.
 * - [title|header|body|footer]: enable reloading.
 *                
 * 			     Upon each toggle the toggle's href will be fetched and the response html is searched for 
 *               divs with the classes .modal-title, .modal-header, .modal-body, .modal-footer
 *               Each found class will replace (or inserting) the modal's div with that class.
 *               
 *               If no class is found within the response html, the div class with the value of the option
 *               will be replaced.
 *                   
 * @copyright 2014 Cross Solution
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;(function($) {
	
	/* Bootstrap modal */
	var Modal = $.fn.modal.Constructor;
	
	/* Constructor of the reloadable modal */
	var ReloadableModal = function (element, options) {
		this.cache     = {};
	    this.options   = options;
	    this.$element  = $(element);
	    this.$backdrop =
	    this.isShown   = null;
	    this.origHtml  = this.$element.html();
	    
	    this.load = function(href)
	    {
	    	if (!this.cache[href]) {
	    		this.$element.html(this.origHtml);
	    		if (true === this.options.reloadable) {
	    			var $this = this;
	    			this.$element.load(href, function() {
	    				$this.cache[href] = $this.$element.html();
	    			});
	    			return;
	    		}
	    		
	    		var $this = this;
	    		$.get(href)
	    		 .done(function(html) {
	    			 var $html = $(html);
	    			 var $element = $this.$element;
	    			 var found = false;
	    			 $html.each(function() {
	    				 var $htmlElement = $(this);
	    				 $.each(['header', 'title', 'body', 'footer'], function(i, name) {
	    					 var className = '.modal-' + name;
	    					 var $elem = $htmlElement.hasClass(className.substr(1))
	    					           ? $htmlElement
	    					           : $htmlElement.find(className);
	    					 
	    					 if ($elem.length) {
	    						 found = true;
	    						 var $orig = $element.find(className);
	    						 if ($orig.length) {
	    							 $orig.html($elem.html());
	    						 }
	    					 }
	    				 });
	    			 });
	    			 
	    			 if (!found && $element.find('.modal-' + $this.options.reloadable).length) {
	    				 $element.find('.modal-' + $this.options.reloadable).html(html);
	    			 }
	    			 $this.cache[href] = $this.$element.html();
	    		 })
	    		 .fail(function() {
	    			 var title = $this.$element.data('error-title');
	    			 var body  = $this.$element.data('error-message');
	    			 
	    			 if (!title) {
	    				 title = 'Error loading content.';
	    			 }
	    			 if (!body) {
	    				 body = '<p>An error occured while fetching the content of this modal box.</p>';
	    			 }
	    			 
	    			 $this.$element.find('.modal-title').html(title);
	    			 $this.$element.find('.modal-body').html(body);
	    			
	    		 });

	    	} else {
	    		this.$element.html(this.cache[href]);
	    	}
	    };
	    	    
	    this.load(this.options.remote);
	};
	
	ReloadableModal.prototype = $.fn.modal.Constructor.prototype;
	
	$.fn.modal = function (option, _relatedTarget) 
	{
		
	    return this.each(function () {
	      var $this   = $(this);
	      var data    = $this.data('bs.modal');
	      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option);
	      if (options['reloadable']) {
	    	  if (!data) {
	    		  data = new ReloadableModal(this, options);
	    		  $this.data('bs.modal', data);
	    	  } else {
	    		  var href = options.remote 
	    		           ? options.remote
	    		           : $(_relatedTarget).attr('href').replace(/.*(?=#[^\s]+$)/, '');
	    		  
	    		  if (!/#/.test(href)) {
	    			  data.load(options.remote);
	    		  }
	    	  }
	      } else {
	    	  data = new Modal(this, options);
	    	  $this.data('bs.modal', data);
	      }
    	
    	if (typeof option == 'string') data[option](_relatedTarget)
    	else if (options.show) data.show(_relatedTarget)
	  });
	};

})(jQuery);
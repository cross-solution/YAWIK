

;(function ($) {
	
	function Container($container)
	{
		this._init($container);
	}
	
	$.extend(Container.prototype, {
		
		submit: function(e, args)
		{
			var _this = this;
			var result = args.data;
			
			if (result.valid) {
                var container = $(e.target).parents(".sf-container");
				console.debug('catch submit', e, result, container);
                container.removeClass('yk-changed');

                // Check if content is returned to replace the summary containers' html.
                // @todo If no content is returned, we must update the field values in the summary,
                // which has to be implemented yet.
                // However, we must not attach a click event listener on the edit button,
                // because the old button is NOT replaced and will call the listener twice!

                if (result.content) {
				    _this.$summaryContainer.html(result.content);
				    _this._initSummaryContainer();
                }
									   
				if ('form' !== result.displayMode) _this.cancel();
			}

            // if we put in a false as return, the bubbling will stop here
            // and no higher JS will know the result of the submit
            // that includes hidden submits like AJAX for Container-Forms
			return true;
		},
		
		cancel: function(event)
		{
			this.$formContainer.animate({opacity: 0, height: 'toggle'});
			this.$summaryContainer.animate({opacity:1, height: 'toggle'});
			return false;
		},
		
		edit: function(event)
		{
			this.$formContainer.animate({opacity:1, height: 'toggle'});
			this.$summaryContainer.animate({opacity: 0, height: 'toggle'});
		},
		
		_initSummaryContainer: function ()
		{
			var $editButton = this.$summaryContainer.find('.sf-edit');
			$editButton.click($.proxy(this.edit, this));
			
			this.$summaryContainer
			    .find('.empty-summary-notice')
			    .click(function() { $editButton.click(); });
		},
		
		_init: function($container)
		{
			this.$mainContainer = $container;
			this.$formContainer = $container.find('.sf-form');
			this.$summaryContainer = $container.find('.sf-summary');
			this._initSummaryContainer();
			
			this.displayMode = 'form';
			if ($container.data('display-mode')) {
				this.displayMode = $container.data('display-mode');
			}
			
			if ('summary' == this.displayMode) {
				this.$formContainer.hide().css('opacity', 0);
			} else {
				this.$summaryContainer.hide().css('opacity', 0);
			}
			
			this.$formContainer.find('form').on('yk.forms.done', $.proxy(this.submit, this))
                                            .find(':input')
                                            .change(function(e) {
                                                $(e.target).parents('.sf-container').addClass("yk-changed");
                                            });
			this.$formContainer.find('.sf-cancel').click($.proxy(this.cancel, this));
			
		}
	});
	
	$.fn.summaryform = function ()
	{
		var containers = {};
		return this.each(function () {
			var $div = $(this);
			new Container($div);
			
		});
	};


    /**
     * ensures that all forms in the summary-form are saved before executing a link
     */
    $.fn.summaryform.ensureSave = function (event)
    {
        if (checkForms() && checkIFrames()) {
            return true;
        }

        event.preventDefault();
        event.stopPropagation();

        return false;

        function checkForms() {
            var result = true;
            $(".sf-container").each(function() {
                var containers = $(this);
                if (containers.hasClass("yk-changed")) {
                    //console.log("test-container", containers, containers.hasClass("yk-changed"));
                    var sfForm = $(this).find(".sf-form");
                    var sfSummary = $(this).find(".sf-summary");
                    var title = $(this).find(".sf-headline").text();
                    if (sfForm.length == 1 && sfSummary.length == 1) {
                        //console.log(title, sfForm.css("display"), sfSummary.css("display"));
                        if (sfForm.css("display") == "block" && sfSummary.css("display") == "none") {
                            var res = confirm("Form '" + title + "' has not been saved\ncontinue ?");
                            if (!res) {
                                // set the return-value and end the loop, so that you don't have to go through all other open forms
                                result = false;
                                // this return is intentional, it ends the each loop primarily - like a break statement would end a normal for-loop
                                return false;
                            }
                            else {
                                containers.removeClass('yk-changed');
                            }
                        }
                    }
                }
            });

            return result;
        }

        function checkIFrames() {

            var iFrames = jQuery("iframe");
            var result = true;

            if (0 < iFrames.length) {
                // look out for tinyMC only if there are iFrames
                var iFrameWindow = $("iframe")[0].contentWindow;
                if (typeof iFrameWindow.tinyMCE != "undefined") {
                    // ensure that the tinyMC-Management is active
                    //console.log('iFrameWindow', iFrameWindow.tinyMCE);
                    for (tinyEditorIndex in iFrameWindow.tinyMCE.editors) {
                        var tinyEditor = iFrameWindow.tinyMCE.editors[tinyEditorIndex];
                        //console.log (tinyEditor, tinyEditor.isNotDirty);
                        if (!tinyEditor.isNotDirty) {
                            var res = confirm("Discard unsaved changes in job description?");
                            if (!res) {
                                // set the return-value and end the loop, so that you don't have to go through all other open forms
                                result = false;
                                // this return is intentional, it ends the each loop primarily - like a break statement would end a normal for-loop
                                return false;
                            } else {
                                //tinyEditor.fire("blur");
                            }
                        }
                    }
                }
            }

            return result;
        }
        //console.log("returnValue", returnValue);
    };

    /**
     * initialize on DocumentReady here
     */
	$(function() {
        $(".sf-container").summaryform();
        $("a").click($.fn.summaryform.ensureSave);
    });
	
})(jQuery);

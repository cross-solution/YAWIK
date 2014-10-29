<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\ElementInterface;

class FormEditor extends FormTextarea
{

    /*
    public function __invoke(ElementInterface $element = null)
    {

    }
    */


    public function render(ElementInterface $element)
    {
        $name   = $element->getName();
        if (empty($name) && $name !== 0) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $renderer = $this->getView();

        /*
        $pluginManager = $renderer->getHelperPluginManager();
        if (!$pluginManager->has('edit')) {
            $pluginManager->setService('edit', $this);
        }
        */

        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');

        $headscript->appendFile($basepath('js/tinymce/tinymce.jquery.min.js'));
        $headscript->prependFile($basepath('js/jquery.min.js'));

        $headscript->offsetSetScript('1000_tinymce', '
        $(document).ready(function() {
            tinyMCE.init({
                //mode : "textareas",
                selector : "div.tinymce",
                inline : true,
                theme : "modern",
                //editor_selector : "tinymce",
                setup: function(editor) {
                    editor.on("blur", function(e) {
                    //console.log("blur event", e);
                    var container = e.target.bodyElement;
                    var form = $(container).parents("form").get(0);
                    console.log("form", form);
                    form.submit();
                    //$(form).on("yk.forms.done", function(){console.log("done")});

                });
    }

            });
        });
        ');

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $attributes['id']   = $name;
        $content            = $element->getValue();
        if (!isset($content)) {
            $content = '';
        }
        if (is_string($content)) {
            // content is should be in an ordinary textarea
            $escapeHtml         = $this->getEscapeHtmlHelper();

            $class = array_key_exists('class',$attributes)?$attributes['class']:'';
            $class .= (empty($class)?:' ') . ' tinymce';
            $attributes['class'] = $class;

            return sprintf(
                '<div %s id="abracadabra">%s</div>',
                $this->createAttributesString($attributes),
                $escapeHtml($content)
            );

            return sprintf(
                '<textarea %s>%s</textarea>',
                $this->createAttributesString($attributes),
                $escapeHtml($content)
            );
        }
        else {
            //$content->injectElement($this);
            return (string) $content;
        }
    }
}
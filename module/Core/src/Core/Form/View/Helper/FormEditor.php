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

    protected $theme = 'modern';
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
        //
        // mode : "textareas",
        $headscript->offsetSetScript('1000_tinymce_' . $this->getTheme() , '
        $(document).ready(function() {
            tinyMCE.init({
                selector : "div.tinymce_' . $this->getTheme() . '",
                inline : true,
                theme : "modern",
                plugins: [
                    "advlist autolink lists charmap anchor",
                    "searchreplace visualblocks code fullscreen",
                    "contextmenu paste"
                ],
                removed_menuitems: "newdocument",' . PHP_EOL
                . $this->additionalOptions() .
                'setup: function(editor) {
                    editor.on("blur", function(e) {
                    //console.log("blur event", e);
                    var container = e.target.bodyElement;
                    var form = $(container).parents("form");
                    //console.log("form", form);
                    editor.save();
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
            $class .= (empty($class)?:' ') . ' tinymce_' . $this->getTheme() ;
            $attributes['class'] = $class;

            return sprintf(
                '<div %s >%s</div>',
                $this->createAttributesString($attributes),
                $content
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

    protected function getTheme() {
        return $this->theme;
    }

    protected function additionalOptions() {
        return '';
    }

}
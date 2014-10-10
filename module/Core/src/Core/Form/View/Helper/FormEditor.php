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

        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');

        $headscript->appendFile($basepath('js/tinymce/tinymce.jquery.min.js'));

        $headscript->offsetSetScript('1000_tinymce', '
        $(document).ready(function() {
            tinyMCE.init({
                mode : "textareas",
                theme : "modern",
                editor_selector : "tinymce",
            });
        });
        ');

        $attributes         = $element->getAttributes();
        $attributes['name'] = $name;
        $content            = (string) $element->getValue();
        $escapeHtml         = $this->getEscapeHtmlHelper();

        $class = array_key_exists('class',$attributes)?$attributes['class']:'';
        $class .= (empty($class)?:' ') . 'tinymce';
        $attributes['class'] = $class;


        return sprintf(
            '<textarea %s>%s</textarea>',
            $this->createAttributesString($attributes),
            $escapeHtml($content)
        );
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormCheckbox as ZfFormCheckbox;
use Zend\Form\ElementInterface;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormInfoCheckbox extends ZfFormCheckbox
{
    
    public function render(ElementInterface $element)
    {
        $input      = parent::render($element);
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();
        $label      = $element->getLabel();
        $desc       = $element->getOption('description');
        
        if ($label) {
            $label = $translator->translate($label, $textDomain);
        }
        /*
         * 'label' => *@translate* 'Privacy Police',
                'description' => /*@translate* 'I have read the %s and accept it',
                'linktext' => /*@translate* 'privacy police',
                'route' => 'lang/applications/disclaimer',
         */
        if ($desc) {
            $desc = $translator->translate($desc, $textDomain);
            $linktext = $element->getOption('linktext');
            $route = $element->getOption('route');
            $params = $element->getOption('params');
            if ($linktext) {
                if ($route) {
                    if (!$params) {
                        $params = array();
                    }
                    $href = $this->getView()->url($route, $params, true);
                } else {
                    /*@todo add more options like providing url... */
                    $href="";
                }
                $linktext = $translator->translate($linktext);
                $link = '<a data-toggle="modal" href="' . $href . '" '
                      . 'data-target="#modal-' . $element->getAttribute('id') . '">'
                      . $linktext . '</a>';
                
                $desc = sprintf($desc, $link);
            }
            $label = '<h6>' . $label . '</h6>';
        } else {
            $desc = $label;
            $label = '';
        }
        
        $markup = '
        <div class="form-checkbox-wrapper">
            <div class="pull-left">%s</div>
            <div class="form-checkbox-label"><label for="%s">%s</label></div>
        </div>
        
        <div class="modal fade" id="modal-' . $element->getAttribute('id') . '">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>';
        
        $markup = sprintf(
            $markup,
            $input, $element->getAttribute('id'), $desc
        );
        
        return $label . $markup;
    }
}
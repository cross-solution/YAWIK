<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        $label      = $element->getOption('long_label');
        $headline   = $element->getOption('headline');


        if ($label) {
            $label = $translator->translate($label, $textDomain);
            $linktext = $element->getOption('linktext');
            $route = $element->getOption('route');
            $params = $element->getOption('params');
            if ($linktext) {
                if ($route) {
                    if (!$params) {
                        $params = array();
                    }
                    /** @noinspection PhpUndefinedMethodInspection */
                    $href = $this->getView()->url($route, $params, true);
                } else {
                    /*@todo add more options like providing url... */
                    $href="";
                }
                $linktext = $translator->translate($linktext);
                $link = '<a data-toggle="modal" href="' . $href . '" '
                      . 'data-target="#modal-' . $element->getAttribute('id') . '">'
                      . $linktext . '</a>';
                
                $label = sprintf($label, $link);
            }
        }

        if ($headline) {
            $headline = '<h6>' . $translator->translate($headline, $textDomain) . '</h6>';
        }
        
        $markup = '
        <div class="checkbox">
            <label for="%s">%s %s</label>
        </div>
        <div class="modal fade modal-scrollable" id="modal-' . $element->getAttribute('id') . '" tabindex="-1" role="dialog" aria-labelledby="label-search-user" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>';
        
        $markup = sprintf(
            $markup,
            $element->getAttribute('id'),
            $input,
            $label
        );
        
        return $headline . $markup;
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\FormLabel;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

class RequiredMarkInFormLabel extends FormLabel
{
    public function __invoke(ElementInterface $element = null, $labelContent = null, $position = null)
    {
        // Set $required to a default of true | existing elements required-value
        $required = ($element->hasAttribute('required') ? true : false);

        if (true === $required) {
            $labelContent = sprintf(
                '%s<span class="required-mark">*</span>',
                $labelContent
            );
        }

        return $this->openTag($element) . $labelContent . $this->closeTag();
    }
}

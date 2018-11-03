<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Jobs\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * Render a form <input> element from the provided $element
 *
 * @param  ElementInterface $element
 * @throws Exception\DomainException
 * @return string
 */
class PreviewLink extends AbstractHelper
{
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }
        return $this->render($element);
    }

    public function render(ElementInterface $element, $options = null)
    {
        $content = '<iframe src="' . $element->getValue() . '" name="preview" style="width:100%; height:800px;"></iframe>';
        return $content;
    }
}

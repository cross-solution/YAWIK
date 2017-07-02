<?php
/**

 */

namespace Core\Form\View\Helper;

use Core\Form\ViewPartialProviderInterface;
use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\ElementInterface;
use Core\Form\Element\ViewHelperProviderInterface as CoreElementInterface;
use Zend\View\Helper\HelperInterface;

class FormElement extends ZendFormElement
{

    /**
     * @param ElementInterface $element
     *
     * @return string
     */
    public function render(ElementInterface $element, $ignoreViewPartial = false)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        if ($element instanceof ViewPartialProviderInterface && !$ignoreViewPartial) {
            $partial = $element->getViewPartial();
            return $renderer->partial($partial, ['element' => $element]);
        }

        if ($element instanceof CoreElementInterface) {
            $helper = $element->getViewHelper();
            if (is_string($helper)) {
                $helper = $renderer->plugin($helper);
            }
            if ($helper instanceof HelperInterface) {
                $helper->setView($renderer);
            }
            return $helper($element);
        }

        return parent::render($element);
    }
}

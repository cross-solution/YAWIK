<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core forms view helpers */
namespace Core\Form\View\Helper;

use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\Container;
use Zend\Form\View\Helper\AbstractHelper;
use Core\Form\SummaryForm as CoreSummaryForm;

/**
 * Helper for rendering form containers
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormContainer extends AbstractHelper
{
    
    /**
     * Invoke as function.
     *
     * Proxies to {@link render()} or returns self.
     *
     * @param  null|Container $container
     * @param string $layout
     * @param array $parameter
     * @return FormContainer|string
     */
    public function __invoke(Container $container = null, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        if (!$container) {
            return $this;
        }
    
        return $this->render($container, $layout, $parameter);
    }
    
    /**
     * Renders the forms of a container.
     *
     * @param Container $container
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function render(Container $container, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        $content = '';

        $content .= $container->renderPre($this->getView());
    
        if ($container instanceof ViewPartialProviderInterface) {
            return $this->getView()->partial($container->getViewPartial(), array('element' => $container));
        }

        if (isset($parameter['render_label']) && $parameter['render_label'] && ($label = $container->getLabel())) {
            $content .= '<div class="container-headline"><h3>' . $this->getView()->translate($label) . '</h3></div>';
        }
        foreach ($container as $element) {
            $content .= $this->renderElement($element, $layout, $parameter);
        }

        $content .= $container->renderPost($this->getView());
        
        return $content;
    }

    public function renderElement($element, $layout, $parameter)
    {
        $parameterPartial = $parameter;
        $content = '';
        if ($element instanceof ExplicitParameterProviderInterface) {
            $parameterPartial = array_merge($element->getParams(), $parameterPartial);
        }
        if ($element instanceof ViewPartialProviderInterface) {
            $parameterPartial = array_merge(array('element' => $element, 'layout' => $layout), $parameterPartial);
            $content .= $this->getView()->partial(
                             $element->getViewPartial(),
                             $parameterPartial
            );
        } elseif ($element instanceof ViewHelperProviderInterface) {
            $helper = $element->getViewHelper();
            if (is_string($helper)) {
                $helper = $this->getView()->plugin($helper);
            }
            $content .= $helper($element);
        } elseif ($element instanceof CoreSummaryForm) {
            $content .= $this->getView()->summaryForm($element);
        } elseif ($element instanceof Container) {
            $content.= $this->render($element, $layout, $parameter);
        } else {
            $content.= $this->getView()->form($element, $layout, $parameter);
        }

        return $content;
    }
}

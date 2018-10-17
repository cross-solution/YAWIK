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
use Core\Form\WizardContainer;
use Zend\Form\View\Helper\AbstractHelper;
use Core\Form\SummaryForm;

/**
 * Helper for rendering form wizard containers
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormWizardContainer extends AbstractHelper
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
     * @param WizardContainer $container
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function render(WizardContainer $container, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        $content = '';

        $content .= $container->renderPre($this->getView());

        $tabsNav = '';
        $tabsContent = '';
        $containerParams = [
            'pager' => true,
            'finish_label' => 'Finish',
            'finish_href' => 'javascript:;',
            'finish_enabled' => true,
        ];

        if (isset($parameter['wizard'])) {
            $containerParams = array_merge($containerParams, $parameter['wizard']);
            unset($parameter['wizard']);
        }

        $translate = $this->getView()->plugin('translate');
        $formContainer = $this->getView()->plugin('formContainer');

        if ($container instanceof ViewPartialProviderInterface) {
            return $this->getView()->partial($container->getViewPartial(), array('element' => $container));
        }

        $containerId = $container->getAttribute('id');
        if (!$containerId) {
            $containerId = 'wizardcontainer-' . strtolower(str_replace('\\', '-', get_class($container)));
        }

        foreach ($container as $tabElement) {
            $tabId = $containerId . '-' . strtolower($tabElement->getName());
            $tabsNav .= '<li><a data-toggle="tab" href="#' . $tabId . '">' . $translate($tabElement->getLabel()) . '</a></li>';
            $tabsContent .= '<div class="tab-pane" id="' . $tabId . '">'
                          . $formContainer($tabElement, $layout, $parameter)
                          . '</div>';
        }

        $content .= '<style type="text/css">.tab-content > div > div:first-child { margin-top: 10px; }</style><div class="wizard-container" id="' . $containerId . '">'
                  . '<ul>' . $tabsNav . '</ul>'
                  . '<div class="tab-content">' . $tabsContent . '</div>';
        if ($containerParams['pager']) {
            $content .='<ul class="pager wizard">'
                  . '<li class="previous"><a href="javascript:;">&larr; ' . $translate('previous') . '</a></li>'
                  . '<li class="next"><a href="javascript:;">' . $translate('Next') . ' &rarr;</a></li>'
                  . '<li class="finish' . ($containerParams['finish_enabled'] ? '' : ' disabled') . '">'
                  . (
                      false !== $containerParams['finish_label']
                     ? '<a class="pull-right" href="' . $containerParams['finish_href'] . '">'
                       . $translate($containerParams['finish_label']) . ' &bull;</a>'
                     : ''
                    )
                  . '</li></ul>';
        }
        $content .= '</div>';

        $content .= $container->renderPost($this->getView());
        
        return $content;
    }
}

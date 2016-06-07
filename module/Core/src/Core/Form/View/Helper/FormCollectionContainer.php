<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Form\View\Helper;

use Core\Form\CollectionContainer;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * Helper for rendering form collection containers
 *
 * @author fedys
 */
class FormCollectionContainer extends AbstractHelper
{

    /**
     * Invoke as function.
     *
     * Proxies to {@link render()} or returns self.
     *
     * @param  null|CollectionContainer $container
     * @param string $layout
     * @param array $parameter
     * @return FormCollectionContainer|string
     */
    public function __invoke(CollectionContainer $container = null, $layout = Form::LAYOUT_HORIZONTAL, $parameter = [])
    {
        if (!$container) {
            return $this;
        }
        
        return $this->render($container, $layout, $parameter);
    }
    
    /**
     * Renders the forms of a container.
     *
     * @param CollectionContainer $container
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function render(CollectionContainer $container, $layout = Form::LAYOUT_HORIZONTAL, $parameter = [])
    {
        $view = $this->getView();
        $view->headscript()
            ->appendFile($view->basePath('Core/js/jquery.formcollection-container.js'));
        $formContainerHelper = $view->formContainer();
        $formsMarkup = '';
        $formTemplateWrapper = '<div class="form-collection-container-form">%s</div>';
        
        foreach ($container as $form) /* @var $form \Zend\Form\Form */
        {
            $formsMarkup .= sprintf($formTemplateWrapper, $formContainerHelper->renderElement($form, $layout, $parameter));
        }
        
        $templateMarkup = sprintf(
            $view->formCollection()->getTemplateWrapper(),
            $view->escapeHtmlAttr(sprintf($formTemplateWrapper, $formContainerHelper->renderElement($container->getTemplateForm(), $layout, $parameter)))
        );
        
		return sprintf('<div class="form-collection-container" data-template-placeholder="%s">
                <h3>%s</h3>
                %s%s%s
            </div>',
            CollectionContainer::TEMPLATE_PLACEHOLDER,
            $container->getLabel(),
            $formsMarkup,
            $templateMarkup,
            '<div class="form-collection-container-add-wrapper"><button type="button" class="btn btn-success form-collection-container-add-button">' . sprintf($this->getTranslator()->translate('Add %s'), $container->getLabel()) . '</button></div>'
        );
    }
}

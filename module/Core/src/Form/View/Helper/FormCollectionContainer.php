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
     * @var bool
     */
    protected $displayRemoveButton = true;

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
            ->appendFile($view->basepath('modules/Core/js/jquery.formcollection-container.js'));
        $translator = $this->getTranslator();
        $formContainerHelper = $view->formContainer();
        $formsMarkup = '';
        $formTemplateWrapper = '<div class="form-collection-container-form">
            '. ($this->displayRemoveButton ? '<button type="button" class="btn btn-sm btn-danger pull-right form-collection-container-remove-button">' . $translator->translate('Remove') . '</button>' : '') . '
            %s
        </div>';
        
        foreach ($container as $form) /* @var $form \Zend\Form\Form */
        {
            $formsMarkup .= sprintf($formTemplateWrapper, $formContainerHelper->renderElement($form, $layout, $parameter));
        }
        
        $templateForm = $container->getTemplateForm();
        $templateMarkup = sprintf(
            $view->formCollection()->getTemplateWrapper(),
            $view->escapeHtmlAttr(sprintf($formTemplateWrapper, $formContainerHelper->renderElement($templateForm, $layout, $parameter)))
        );
        
        return sprintf(
            '<div class="form-collection-container" data-new-entry-key="%s" data-remove-action="%s" data-remove-question="%s">
                <h3>%s</h3>
                %s%s%s
            </div>',
            CollectionContainer::NEW_ENTRY,
            $container->formatAction('remove'),
            $translator->translate('Really remove?'),
            $container->getLabel(),
            $formsMarkup,
            $templateMarkup,
            '<div class="form-collection-container-add-wrapper"><button type="button" class="btn btn-success form-collection-container-add-button"><span class="yk-icon yk-icon-plus"></span> ' . sprintf($translator->translate('Add %s'), $container->getLabel()) . '</button></div>'
        );
    }
    /**
     * @param boolean $displayRemoveButton
     * @return FormCollectionContainer
     * @since 0.26
     */
    public function setDisplayRemoveButton($displayRemoveButton)
    {
        $this->displayRemoveButton = (bool)$displayRemoveButton;
        
        return $this;
    }
}

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
     * @param CollectionContainer $container
     * @return string
     */
    public function __invoke(CollectionContainer $container)
    {
        $markup = '';
        $view = $this->getView();
        $view->headscript()
            ->appendFile($view->basePath('Core/js/jquery.formcollection-container.js'));
        
        foreach ($container->getCollections() as $collection) /* @var $collection \Zend\Form\Element\Collection */
        {
            $groupMarkup = '';
            $templateMarkup = '';
            
            foreach ($container->getGroup($collection) as $form)
            {
    			$groupMarkup .= $view->summaryForm($form);
            }
            
            $templateForm = $container->getTemplateForm($collection);
            
            if ($templateForm)
            {
                $templateMarkup = sprintf(
                    $view->formCollection()->getTemplateWrapper(),
                    $view->escapeHtmlAttr($view->summaryForm($templateForm))
                );
            }
            
            $collectionLabel = $collection->getLabel();
			$markup .= sprintf('<div class="form-collection-container">
                    <h3>%s</h3>
                    %s%s%s
                </div>',
                $collectionLabel,
                $groupMarkup,
                $templateMarkup,
                '<div><button type="button" class="btn btn-success form-collection-container-add">' . sprintf($this->getTranslator()->translate('Add %s'), $collectionLabel) . '</button></div>'
            );
        }
        
        return $markup;
    }
}

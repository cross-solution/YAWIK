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

use Core\Form\SummaryFormInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;

/**
 * Helper to render a summary form container.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class SummaryForm extends AbstractHelper
{
    
    /**
     * Invoke as function.
     * 
     * @param null|SummaryFormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return \Core\Form\View\Helper\SummaryForm|string
     */
    public function __invoke(SummaryFormInterface $form = null, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        if (null === $form) {
            return $this;
        }
        
        $mode = $form->getRenderMode();
        if (SummaryFormInterface::RENDER_FORM == $mode) {
            return $this->renderForm($form, $layout, $parameter);
        }
        if (SummaryFormInterface::RENDER_SUMMARY == $mode) {
            return $this->renderSummary($form);
        }
        
        return $this->render($form, $layout, $parameter);
    }
    
    /**
     * Renders a summary form container.
     * 
     * @param SummaryFormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function render(SummaryFormInterface $form, $layout=Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        $renderer = $this->getView();
        $renderer->headscript()->appendFile($renderer->basePath('Core/js/jquery.summary-form.js'));
        
        $label = $form->getLabel();
        $labelContent = $label ? '<div class="sf-headline"><h3>' . $this->getView()->translate($label) . '</h3></div>' : '';
        $formContent  = $this->renderForm($form, $layout, $parameter);
        $summaryContent = $this->renderSummary($form);  
        
        $markup = '<div id="sf-%s" class="sf-container" data-display-mode="%s">'
                . '%s'
                . '<div class="sf-form">%s</div>'
                . '<div class="sf-summary">%s</div>'
                . '</div>';
        
        $content = sprintf(
            $markup,
            $form->getAttribute('name'), $form->getDisplayMode(), $labelContent, $formContent, $summaryContent
        );
        
        
        return $content;
    }
    
    /**
     * Only renders the form representation of a summary form.
     * 
     * @param SummaryFormInterface $form
     * @param string $layout
     * @param array $parameter
     */
    public function renderForm(SummaryFormInterface $form, $layout=Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
        $formHelper = $this->getView()->plugin('form');
        return $formHelper->renderBare($form, $layout, $parameter);
    }
    
    /**
     * Only renders the summary representation of a summary form
     * 
     * @param SummaryFormInterface $form
     * @return string
     */
    public function renderSummary(SummaryFormInterface $form)
    {
        return  '<button type="button" class="pull-right btn btn-default sf-edit">'
              . '<span class="yk-icon yk-icon-edit"></span> '
              . $this->getView()->translate('Edit')
              . '</button>'
              . $this->renderSummaryElement($form->getBaseFieldset());
    }
    
    /**
     * Helper function to recurse into form elements when rendering summary.
     * 
     * @param ElementInterface $element
     * @return string
     */
    protected function renderSummaryElement(ElementInterface $element)
    {
        if ($element instanceOf Hidden || false === $element->getOption('render_summary')) {
            return '';
        }
        if ($element instanceOf ViewPartialProviderInterface) {
            $partial        = $element->getViewPartial();
            $summaryPartial = $partial . '.summary';
            $partialParams  = array(
                'element' => $element
            );
            if (!$this->getView()->resolver($summaryPartial)) {
                $summaryPartial = $partial;
                $partialParams['renderSummary'] = true;
            }
    
            return $this->getView()->partial($summaryPartial, $partialParams);
        }
    
        $label  = $element->getLabel();
        $markup = '';
        if ($element instanceOf FieldsetInterface) {
            if (!$element instanceOf FormInterface && $label) {
                $markup .= '<h4>' . $label . '</h4>';
            }
            foreach ($element as $el) {
                $markup .= $this->renderSummaryElement($el);
            }
            return $markup;
        }
    
        $elementValue = $element instanceOf \Zend\Form\Element\Textarea 
                      ? nl2br($element->getValue()) 
                      : $element->getValue();
                      
        $markup .= '<div class="row">'; $col = 12;
        if ($label) {
            $markup .= '<div class="col-md-3"><strong>' . $label . '</strong></div>';
            $col = 9;
        }
        $markup .= '<div class="col-md-' . $col . '">' . $elementValue . '</div>'
            . '</div>';
        return $markup;
    }
    
}
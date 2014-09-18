<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */ 
namespace Core\Form\View\Helper;

use Core\Form\SummaryFormInterface;
use Zend\Form\Element\Hidden;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\DescriptionAwareFormInterface;
use Core\Form\EmptySummaryAwareInterface;

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
        
        $formContent = sprintf(
                '<div class="sf-form"><div class="panel panel-info"><div class="panel-body">%s</div></div></div>
                 <div class="sf-summary">%s</div>
                ',
                $formContent, $summaryContent
        );
        
        if ($form instanceOf DescriptionAwareFormInterface && $form->isDescriptionsEnabled()) {
            $this->getView()->headscript()->appendFile(
                $this->getView()->basepath('Core/js/forms.descriptions.js')
            );
        
            if ($desc = $form->getOption('description', '')) {
                $translator = $this->getTranslator();
                $textDomain = $this->getTranslatorTextDomain();
        
                $desc = $translator->translate($desc, $textDomain);
            }
        
            $formContent = sprintf(
                '<div class="daf-form-container row">
                        <div class="daf-form col-md-8">%s</div>
                        <div class="daf-desc col-md-4">
                            <div class="daf-desc-content alert alert-info">%s</div>
                        </div>
                    </div>',
                $formContent, $desc
            );
        } 
        
        $markup = '<div id="sf-%s" class="sf-container" data-display-mode="%s">'
                . '%s'
                . '%s'
                . '</div>';
        
        $content = sprintf(
            $markup,
            $form->getAttribute('name'), $form->getDisplayMode(), $labelContent, $formContent
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
                                                    /* @var $form SummaryFormInterface|\Core\Form\SummaryForm */
        $renderer     = $this->getView();           /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $formHelper   = $renderer->plugin('form');  /* @var $formHelper \Core\Form\View\Helper\Form */
        $fieldset     = $form->getBaseFieldset();
        $resetPartial = false;

        if ($fieldset instanceOf ViewPartialProviderInterface) {
            $origPartial = $fieldset->getViewPartial();
            $partial     = "$origPartial.form";
            if ($renderer->resolver($partial)) {
                $fieldset->setViewPartial($partial);
                $resetPartial = true;
            }
        }

        $markup = $formHelper->renderBare($form, $layout, $parameter);

        if ($resetPartial) {
            /** @noinspection PhpUndefinedVariableInspection */
            $fieldset->setViewPartial($origPartial);
        }

        return $markup;
    }
    
    /**
     * Only renders the summary representation of a summary form
     * 
     * @param SummaryFormInterface $form
     * @return string
     */
    public function renderSummary(SummaryFormInterface $form)
    {
        return  '<div class="panel panel-default" style="min-height: 100px;">
                    <div class="panel-body"><button type="button" class="pull-right btn btn-default btn-xs sf-edit">'
              . '<span class="yk-icon yk-icon-edit"></span> '
              . $this->getView()->translate('Edit')
              . '</button>'
              . $this->renderSummaryElement($form->getBaseFieldset())
              . '</div></div>';
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
            $renderer    = $this->getView();                 /* @var $renderer \Zend\View\Renderer\PhpRenderer */
            $origPartial = $element->getViewPartial();
            $partial     = "$origPartial.view";
            $partialParams  = array(
                'element' => $element
            );
            if (!$renderer->resolver($partial)) {
                $partial = $origPartial;
                $partialParams['renderSummary'] = true;
            }
    
            return $renderer->partial($partial, $partialParams);
        }
        
        if ($element instanceOf EmptySummaryAwareInterface && $element->isSummaryEmpty()) {
            /* @var $element EmptySummaryAwareInterface|ElementInterface */
            $emptySummaryNotice = $this->getTranslator()->translate(
                $element->getEmptySummaryNotice(), $this->getTranslatorTextDomain()
            );
            
            $markup = sprintf(
                '<div id="%s-empty-alert" class="empty-summary-notice alert alert-info"><p>%s</p></div>',
                $element->getAttribute('id'), $emptySummaryNotice
            );
            return $markup;
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
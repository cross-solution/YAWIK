<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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
    public function render(SummaryFormInterface $form, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
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
            $formContent,
            $summaryContent
        );
        
        if ($form instanceof DescriptionAwareFormInterface && $form->isDescriptionsEnabled()) {
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
                $formContent,
                $desc
            );
        }
        
        $markup = '<div id="sf-%s" class="sf-container" data-display-mode="%s">'
                . '%s'
                . '%s'
                . '</div>';
        
        $id = str_replace('.','-',$form->getAttribute('name'));
        $content = sprintf(
            $markup,
            $id,
            $form->getDisplayMode(),
            $labelContent,
            $formContent
        );
        
        
        return $content;
    }

    /**
     * Only renders the form representation of a summary form.
     *
     * @param SummaryFormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function renderForm(SummaryFormInterface $form, $layout = Form::LAYOUT_HORIZONTAL, $parameter = array())
    {
                                                    /* @var $form SummaryFormInterface|\Core\Form\SummaryForm */
        $renderer     = $this->getView();           /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $formHelper   = $renderer->plugin('form');  /* @var $formHelper \Core\Form\View\Helper\Form */
        $fieldset     = $form->getBaseFieldset();
        $resetPartial = false;

        if ($fieldset instanceof ViewPartialProviderInterface) {
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
        $form->prepare();
        $baseFieldset = $form->getBaseFieldset();
        if (!isset($baseFieldset)) {
            throw new \InvalidArgumentException('For the Form ' . get_class($form) . ' there is no Basefieldset');
        }

        $dataAttributesMarkup = '';
        
        foreach ($form->getAttributes() as $dataKey => $dataValue)
        {
            if (preg_match('/^data-/', $dataKey))
            {
                $dataAttributesMarkup .= sprintf(' %s="%s"', $dataKey, $dataValue);
            }
        }
        
        $markup = '<div class="panel panel-default" style="min-height: 100px;"' . $dataAttributesMarkup . '>
                    <div class="panel-body"><div class="sf-controls">%s</div>%s</div></div>';

        $view = $this->getView();
        $buttonMarkup = false === $form->getOption('editable')
                      ? ''
                      : '<button type="button" class="btn btn-default btn-xs sf-edit">'
                        . '<span class="yk-icon yk-icon-edit"></span> '
                        . $view->translate('Edit')
                        . '</button>';
        
        if (($controlButtons = $form->getOption('control_buttons')) !== null)
        {
            $buttonMarkup .= PHP_EOL . implode(PHP_EOL, array_map(function (array $buttonSpec) use ($view) {
                return '<button type="button" class="btn btn-default btn-xs' . (isset($buttonSpec['class']) ? ' ' . $buttonSpec['class'] : '') . '">'
                    . (isset($buttonSpec['icon']) ? '<span class="yk-icon yk-icon-' . $buttonSpec['icon'] . '"></span> ' : '')
                    . $view->translate($buttonSpec['label'])
                    . '</button>';
            }, $controlButtons));
        }

        $elementMarkup = $this->renderSummaryElement($baseFieldset);


        return sprintf($markup, $buttonMarkup, $elementMarkup);
    }
    
    /**
     * Helper function to recurse into form elements when rendering summary.
     *
     * @param ElementInterface $element
     * @return string
     */
    public function renderSummaryElement(ElementInterface $element)
    {
        if ($element instanceof Hidden || false === $element->getOption('render_summary')) {
            return '';
        }

        if ($element instanceof EmptySummaryAwareInterface && $element->isSummaryEmpty()) {
            /* @var $element EmptySummaryAwareInterface|ElementInterface */
            $emptySummaryNotice = $this->getTranslator()->translate(
                $element->getEmptySummaryNotice(),
                $this->getTranslatorTextDomain()
            );

            $markup = sprintf(
                '<div id="%s-empty-alert" class="empty-summary-notice alert alert-info"><p>%s</p></div>',
                $element->getAttribute('id'),
                $emptySummaryNotice
            );
            return $markup;
        }

        if ($element instanceof ViewPartialProviderInterface) {
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
    
        $label  = $this->getTranslator()->translate($element->getLabel());
        $markup = '';
        
        if ($element instanceof FieldsetInterface) {
            if (!$element instanceof FormInterface && $label) {
                $markup .= '<h4>' . $label . '</h4>';
            }
            foreach ($element as $el) {
                $markup .= $this->renderSummaryElement($el);
            }
            return $markup;
        }
    
        $elementValue = $element instanceof \Zend\Form\Element\Textarea
                      ? nl2br($element->getValue())
                      : $element->getValue();

        if ('' != $elementValue && $element instanceof \Zend\Form\Element\Select) {
            if ($summaryValue = $element->getOption('summary_value')) {
                $elementValue = is_callable($summaryValue) ? $summaryValue() : $summaryValue;
            } else {
                $options = $element->getValueOptions();
                $translator = $this->getTranslator();
                if (true == $element->getAttribute('multiple')) {

                    $multiOptions = [];
                    foreach ($elementValue as $optionKey) {
                        if (isset($options[$optionKey])) {
                            $multiOptions[] = $translator->translate($options[$optionKey]);
                            continue;
                        }

                        foreach ($options as $optKey => $optVal) {
                            if (!is_array($optVal) || !array_key_exists($optionKey, $optVal['options'])) { continue; }

                            $optGroupLabel = isset($optVal['label']) ? $translator->translate($optVal['label']) : $optKey;
                            $multiOptions[] = $optGroupLabel . ' | ' . $translator->translate($optVal['options'][$optionKey]);
                        }
                    }

                    $elementValue = '<ul><li>' . join('</li><li>' , $multiOptions) . '</li></ul>';

                } else {
                    $elementValue = $translator->translate($options[$elementValue]);
                }
            }
        }

        if ('' != $elementValue && $element instanceOf \Zend\Form\Element\File) {
            return '';
        }
                      
        $markup .= '<div class="row">';
        $col = 12;
        if ($label) {
            $markup .= '<div class="col-md-3 yk-label"><label>' . $label . '</label></div>';
            $col = 9;
        }
        $markup .= '<div class="col-md-' . $col . '">' . $elementValue . '</div>'
            . '</div>';
        return $markup;
    }
}

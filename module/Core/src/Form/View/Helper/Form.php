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

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\DescriptionAwareFormInterface;

/**
 * Helper to render a formular.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Form extends ZendForm
{
    /**#@+
     * Layout constants.
     * @var string
     */
    const LAYOUT_HORIZONTAL = 'form-horizontal';
    const LAYOUT_INLINE     = 'form-inline';
    const LAYOUT_VERTICAL   = '';
    const LAYOUT_BARE       = 'form-bare';
    /**#@-*/
    
    
    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function __invoke(FormInterface $form = null, $layout = self::LAYOUT_HORIZONTAL, $parameter = array())
    {
        if (!$form) {
            return $this;
        }
    
        return $this->render($form, $layout, $parameter);
    }
    
    /**
     * Render a form from the provided $form,
     *
     * @param FormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function renderBare(FormInterface $form, $layout = self::LAYOUT_HORIZONTAL, $parameter = array())
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer
         * @var $headscript \Zend\View\Helper\HeadScript
         * @var $basepath \Zend\View\Helper\BasePath */
        $renderer   = $this->getView();
        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');
        
        $headscript->appendFile($basepath('modules/Core/js/core.spinnerbutton.js'))
                   //->appendFile($basepath('/assets/select2/js/select2.min.js'))
                   ->appendFile($basepath('modules/Core/js/core.forms.js'));

        /* @noinspection PhpParamsInspection */
        //$renderer->headLink()->appendStylesheet($basepath('/assets/select2/css/select2.css'));

        if ($scripts = $form->getOption('headscript')) {
            if (!is_array($scripts)) {
                $scripts = array($scripts);
            }
            foreach ($scripts as $script) {
                $headscript->appendFile($basepath($script));
            }
        }
        
        $class = $form->getAttribute('class');
        $class = preg_replace('~\bform-[^ ]+\b~', '', $class);
        $class .= ' ' . $layout;
        
        $form->setAttribute('class', $class);

        $formId = $form->getAttribute('id') ?: $form->getName();
        $formId = preg_replace(
            array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
            array('-'              , '-'    , ''       ),
            $formId
        );
        $form->setAttribute(
            'id',
            $formId

        );

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
    
        $formContent = '';
    
        if ($form instanceof ViewPartialProviderInterface) {
            return $renderer->partial($form->getViewPartial(), array_merge(['element' => $form], $parameter));
        }

        /* @var $element \Zend\Form\ElementInterface */
        foreach ($form as $element) {
            $parameterPartial = $parameter;
            $elementId = $element->getAttribute('id');
            if (!$elementId) {
                $elementId = preg_replace(
                    array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                    array('-'             , '-',     ''),
                    $element->getName()
                );
            }
            $element->setAttribute('id', "$formId-$elementId");
            $element->setOption('__form_id__', $formId);
            if ($element instanceof ExplicitParameterProviderInterface) {
                /* @var $element ExplicitParameterProviderInterface */
                $parameterPartial = array_merge($element->getParams(), $parameterPartial);
            }

            if ($element instanceof ViewPartialProviderInterface) {
                /* @var $element ViewPartialProviderInterface */
                $parameterPartial = array_merge(array('element' => $element, 'layout' => $layout), $parameterPartial);
                /** @noinspection PhpToStringImplementationInspection */
                $formContent .= $renderer->partial(
                    $element->getViewPartial(),
                    $parameterPartial
                );
            } elseif ($element instanceof FieldsetInterface) {
                if ($element instanceof ViewHelperProviderInterface) {
                    /* @var $element ViewHelperProviderInterface */
                    $helper = $element->getViewHelper();
                    if (is_string($helper)) {
                        $helper = $renderer->plugin($helper);
                    }

                    $formContent .= $helper($element);
                } else {
                    $formContent .= $renderer->formCollection($element, true, $layout);
                }
            } elseif (false !== $element->getOption('use_formrow_helper')) {
                $formContent .= $renderer->formRow($element, null, null, $layout);
            } else {
                $formContent .= $renderer->formElement($element);
            }
        }
        
        return $this->openTag($form) . $formContent . $this->closeTag();
    }
    
    /**
     * Renders a form from the provided form.
     * Wraps this form in a div-container and renders the label,
     * if any.
     *
     * @param FormInterface $form
     * @param string $layout
     * @param array $parameter
     * @uses renderBare()
     * @see \Zend\Form\View\Helper\Form::render()
     * @return string
     */
    public function render(FormInterface $form, $layout = self::LAYOUT_HORIZONTAL, $parameter = array())
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $formContent = $this->renderBare($form, $layout, $parameter);
        $renderer    = $this->getView();
        
        if ($form instanceof DescriptionAwareFormInterface && $form->isDescriptionsEnabled()) {
            /* @var $form DescriptionAwareFormInterface|FormInterface */
            $renderer->headscript()->appendFile(
                    $renderer->basepath('modules/Core/js/forms.descriptions.js')
                );
                
            if ($desc = $form->getOption('description', '')) {
                $descriptionParams=$form->getOption('description_params');
                $translator = $this->getTranslator();
                $textDomain = $this->getTranslatorTextDomain();
                $desc = $translator->translate($desc, $textDomain);
                if ($descriptionParams) {
                    array_unshift($descriptionParams, $desc);
                    $desc = call_user_func_array('sprintf', $descriptionParams);
                }
            }
                
            $formContent = sprintf(
                    '<div class="daf-form-container row">
                        <div class="daf-form col-md-8"><div class="panel panel-default"><div class="panel-body">%s</div></div></div>
                        <div class="daf-desc col-md-4">
                            <div class="daf-desc-content alert alert-info">%s</div>
                        </div>
                    </div>',
                    $formContent,
                    $desc
                );
        } else {
            $formContent = '<div class="form-content">' . $formContent . '</div>';
        }
        
        $markup = '<div id="form-%s" class="form-container">'
                . '%s'
                . '%s'
                . '</div>';
        
        if ($label = $form->getLabel()) {
            $label = '<div class="form-headline"><h3>' . $renderer->translate($label) . '</h3></div>';
        }
        
        return sprintf(
            $markup,
            $form->getAttribute('id') ?: $form->getName(),
            $label,
            $formContent
        );
    }
}

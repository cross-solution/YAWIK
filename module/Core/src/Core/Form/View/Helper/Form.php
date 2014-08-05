<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
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
    /**#@-*/
    
    
    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @param string $layout
     * @param array $parameter
     * @return string
     */
    public function __invoke(FormInterface $form = null, $layout=self::LAYOUT_INLINE, $parameter = array())
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
    public function renderBare(FormInterface $form, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        $renderer   = $this->getView();
        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');
        
        $headscript->appendFile($basepath('Core/js/core.spinnerbutton.js'))
                   ->appendFile($basepath('Core/js/core.forms.js'));
        
        if ($scripts = $form->getOption('headscript')) {
            if (!is_array($scripts)) {
                $scripts = Array($scripts);
            }
            foreach ($scripts as $script) {
                $headscript->appendFile($basepath($script));
            }
        }
        
        $class = $form->getAttribute('class');
        $class = preg_replace('~\bform-[^ ]+\b~', '', $class);
        $class .= ' ' . $layout;
        
        $form->setAttribute('class', $class);

        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
    
        $formContent = '';
    
        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), array('element' => $form));
        }
        foreach ($form as $element) {
            $parameterPartial = $parameter;
            if (!$element->hasAttribute('id')) {
                
                $elementId = preg_replace(
                    array('~[^A-Za-z0-9_-]~', '~--+~', '~^-|-$~'),
                    array('-'             , '-',     ''),
                    $form->getName() . '-' . $element->getName()
                );
                $element->setAttribute('id', $elementId);
            }
            if ($element instanceOf ExplicitParameterProviderInterface) {
                $parameterPartial = array_merge($element->getParams(), $parameterPartial);
            }
            if ($element instanceOf ViewPartialProviderInterface) {
                $parameterPartial = array_merge(array('element' => $element, 'layout' => $layout), $parameterPartial);
                $formContent .= $this->getView()->partial(
                    $element->getViewPartial(), $parameterPartial 
                );
                
            } else if ($element instanceOf ViewHelperProviderInterface) {
                $helper = $element->getViewHelper();
                if (is_string($helper)) {
                    $helper = $this->getView()->plugin($helper);
                }
                $formContent .= $helper($element);
            } else if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element, true, $layout);
            } else {
                $formContent.= $this->getView()->formRow($element, null, null, $layout);
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
    public function render(FormInterface $form, $layout=self::LAYOUT_INLINE, $parameter = array())
    {
        $formContent = $this->renderBare($form, $layout, $parameter);
        
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
        } else {
            $formContent = '<div class="form-content">' . $formContent . '</div>';
        }
        
        $markup = '<div id="form-%s" class="form-container">'
                . '%s'
                . '%s'
                . '</div>';
        
        if ($label = $form->getLabel()) {
            $label = '<div class="form-headline"><h3>' . $this->getView()->translate($label) . '</h3></div>';
        }
        
        return sprintf(
            $markup,
            $form->getAttribute('id') ?: $form->getName(),
            $label,
            $formContent
        );
    }
}
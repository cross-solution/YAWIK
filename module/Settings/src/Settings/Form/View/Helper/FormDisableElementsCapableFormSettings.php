<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Settings\Form\View\Helper;

use Settings\Form\Element\DisableElementsCapableFormSettings;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormInput;

/**
 * Renders a disable form elements toggle checkboxes element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FormDisableElementsCapableFormSettings extends FormInput
{
    /**
     * Renders a disabe form elements toggle checkboxes element.
     */
    public function render(ElementInterface $element)
    {
        /* @var $element DisableElementsCapableFormSettings
         * @var $renderer   \Zend\View\Renderer\PhpRenderer
         * @var $headscript \Zend\View\Helper\HeadScript
         * @var $basepath   \Zend\View\Helper\BasePath */
        $renderer   = $this->getView();
        $headscript = $renderer->plugin('headscript');
        $basepath   = $renderer->plugin('basepath');

        $headscript->appendFile($basepath('Settings/js/forms.decfs.js'));

        return '<ul class="disable-elements-list" id="' . $element->getAttribute('id') . '-list"' . '>'
               . $this->renderCheckboxes($element->getCheckboxes())
               . '</ul>';

    }

    /**
     * Recursively renders checkboxes.
     *
     * @param array $checkboxes the checkboxes spec array
     *
     * @return string
     */
    protected function renderCheckboxes($checkboxes)
    {
        $markup = '';
        foreach ($checkboxes as $boxes) {
            $markup .= '<li class="disable-element">';
            if (is_array($boxes)) {
                if (isset($boxes['__all__'])) {
                    $markup .= $this->renderCheckbox($boxes['__all__'], 'disable-elements-toggle');
                    unset($boxes['__all__']);
                }

                $markup .= '<ul class="disable-elements">' . $this->renderCheckboxes($boxes) . '</ul>';
            } else {
                $markup .= $this->renderCheckbox($boxes);
            }
            $markup .= '</li>';
        }
        return $markup;
    }

    /**
     * Renders a checkbox.
     *
     * @param \Core\Form\Element\Checkbox $box
     * @param null|string $class
     *
     * @return string
     */
    protected function renderCheckbox($box, $class = null)
    {
        /* @var $renderer \Zend\View\Renderer\PhpRenderer */
        $renderer = $this->getView();
        if (null !== $class) {
            $box->setAttribute('class', $box->getAttribute('class') . ' ' . $class);
        }
        $markup = $renderer->formCheckBox($box);
        if ($desc = $box->getOption('description')) {
            $desc = $this->getTranslator()->translate($desc, $this->getTranslatorTextDomain());
            $markup .= '<div class="alert alert-info"><p>' . $desc . '</p></div>';
        }
        return $markup;
    }
}

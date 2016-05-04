<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\View\Helper;

use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\TextSearchFormFieldset;
use Core\Form\ViewPartialProviderInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\Form;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class SearchForm extends Form
{

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @param array $colMap
     * @return Form|string
     */
    public function __invoke(FormInterface $form = null, $colMap=null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form, $colMap);
    }

    public function render(FormInterface $form, $colMap=null, $buttonsSpan = null)
    {
        $headscript = $this->getView()->plugin('headscript');
        $basepath   = $this->getView()->plugin('basepath');

        $headscript->appendFile($basepath('Core/js/core.searchform.js'));

        if (is_int($colMap)) {
            $buttonsSpan = $colMap;
            $colMap = null;
        }

        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), [ 'element' => $form, 'colMap' => $colMap, 'buttonsSpan' => $buttonsSpan ]);
        }

        $elements = $form->getElements();
        $buttons  = $form->getButtons();

        $content = $this->renderElements($elements, $buttons, $colMap, $buttonsSpan);

        return $this->openTag($form)
             . '<div class="row" style="padding: 0 15px;">'
             . $content . '</div>' . $this->closeTag();

    }

    public function renderButtons($fieldset)
    {
        return $this->renderElements($fieldset, true);
    }

    public function renderElements($fieldset, $buttonsFieldset, $colMap = null, $buttonsSpan = null)
    {
        if ($fieldset instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($fieldset->getViewPartial(), [ 'element' => $fieldset, 'colMap' => $colMap, 'buttonsSpan' => $buttonsSpan ]);
        }

        if (true !== $buttonsFieldset && null === $colMap) {
            $colMap = $fieldset->getColumnMap();
        }

        $formElement = $this->getView()->plugin('formElement');
        $content = ''; $buttonsRendered = false; $i=0;
        foreach ($fieldset as $element) {
            if (true === $buttonsFieldset) {
                $content .= $formElement($element);
                continue;
            }

            if (isset($colMap[$element->getName()])) {
                $cols = $colMap[$element->getName()];

            } else if (isset($colMap[$i])) {
                $cols = $colMap[$i];

            } else {
                $cols = $element->getOption('span') ?: 12;
            }

            if ($fieldset instanceOf TextSearchFormFieldset && $element->getName() == $fieldset->getButtonElement()) {
                $content.='<div class="input-group col-md-' . $cols . '">'
                              . $formElement($element)
                              . '<div class="input-group-btn search-form-buttons" style="width: 0px;">'
                              . $this->renderElements($buttonsFieldset, true) . '</div>'
                              . '</div>';
                $buttonsRendered = true;
            } else {
                $content .= '<div class="input-group col-md-' . $cols . '">'
                          . $formElement($element)
                          . '</div>';
            }

            $i += 1;
        }

        if (true !== $buttonsFieldset && !$buttonsRendered) {
            if (null === $buttonsSpan) {
                $buttonsSpan = $buttonsFieldset->getSpan();
            }
            $content .= '<div class="input-group search-form-buttons col-md-' . $buttonsSpan . ' text-right">'
                      . '<div class="btn-group">' . $this->renderElements($buttonsFieldset, true) .'</div></div>';
        }

        return $content;
    }
}
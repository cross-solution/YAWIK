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
use Zend\Form\Element\Hidden;
use Zend\Form\ElementPrepareAwareInterface;
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

        $headscript
            ->appendFile($basepath('Core/js/core.forms.js'))
            ->appendFile($basepath('Core/js/core.searchform.js'));

        if (is_int($colMap)) {
            $buttonsSpan = $colMap;
            $colMap = null;
        }

        $form->prepare();

        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), [ 'element' => $form, 'colMap' => $colMap, 'buttonsSpan' => $buttonsSpan ]);
        }

        $content = $this->renderElements($form, $colMap, $buttonsSpan);

        return $this->openTag($form)
             . '<div class="row" style="padding: 0 15px;">'
             . $content . '</div>' . $this->closeTag();

    }

    public function renderButtons($buttons)
    {
        $helper = $this->getView()->plugin('formButton');

        $content = '';
        foreach ($buttons as $button) {
            $button->removeAttribute('name');
            $attrs = $helper->createAttributesString($button->getAttributes());

            $content.= '<button ' . $attrs . '>'
                       . $this->getTranslator()->translate($button->getLabel(), $this->getTranslatorTextDomain())
                       . '</button>';
        }

        return $content;
    }

    public function renderElements($form, $colMap = null, $buttonsSpan = null)
    {
        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), [ 'element' => $form, 'colMap' => $colMap, 'buttonsSpan' => $buttonsSpan ]);
        }

        if (null === $colMap) {
            $colMap = $form->getColumnMap();
        }

        $formElement = $this->getView()->plugin('formElement');
        $content = ''; $buttonsRendered = false; $i=0;
        foreach ($form as $element) {

            if ($element instanceOf Hidden) {
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

            if ($element->getName() == $form->getButtonElement()) {
                $content.='<div class="input-group col-md-' . $cols . '">'
                              . $formElement($element)
                              . '<div class="input-group-btn search-form-buttons" style="width: 1px;">'
                              . $this->renderButtons($form->getButtons()) . '</div>'
                              . '</div>';
                $buttonsRendered = true;
            } else {
                $content .= '<div class="input-group col-md-' . $cols . '">'
                          . $formElement($element)
                          . '</div>';
            }

            $i += 1;
        }

        if (!$buttonsRendered) {
            if (null === $buttonsSpan) {
                $buttonsSpan = $form->getOption('buttons_span') ?: 12;
            }
            $content .= '<div class="input-group search-form-buttons col-md-' . $buttonsSpan . ' text-right">'
                      . '<div class="btn-group">' . $this->renderButtons($form->getButtons()) .'</div></div>';
        }

        return $content;
    }
}

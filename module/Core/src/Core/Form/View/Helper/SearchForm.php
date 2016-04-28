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
    public function __invoke(FormInterface $form = null, $colMap=[])
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form, $colMap);
    }

    public function render(FormInterface $form, $colMap=[])
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $params = $this->getView()->params()->fromQuery();
        $form->setAttribute('data-search-params', \Zend\Json\Json::encode($params));

        $formContent = '<div class="row" style="padding: 0 15px;">'; $buttonsRendered = false;
        $buttonsContent = '<input type="submit" class="btn btn-primary" name="submit" value="' . $this->getView()->translate('Search') . '">'
                          . '<input type="reset" class="btn btn-default" name="clear" value="' . $this->getView()->translate('Clear') . '">';


        if ($form instanceOf ViewPartialProviderInterface) {
            return $this->getView()->partial($form->getViewPartial(), [ 'element' => $form, 'buttons' => $buttonsContent ]);
        }

        if (empty($colMap)) {
            $c = count($form);
            $r = floor($c / 3);

            if (0 != $r) {
                for ($i=0; $i<$r; $i+=1) {
                    $colMap[] = 4;
                    $colMap[] = 4;
                    $colMap[] = 4;
                }
            }
            if ($l = $c % 3) {
                for ($i=0; $i<$l; $i+=1) {
                    $colMap[] = 12 / $l;
                }
            }
        }

        $i = 0;
        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                trigger_error('Fieldsets are not allowed in a search form.', E_USER_NOTICE);
                continue;
            } else {
                $col = isset($colMap[$element->getName()])
                    ? $colMap[$element->getName()]
                    : (isset($colMap[$i]) ? $colMap[$i] : 4);
                $i += 1;

                if ($element->getName() == $form->getOption('button_element')) {
                    $formContent.='<div class="input-group col-md-' . $col . '">'
                                 . $this->getView()->formElement($element)
                                 . '<div class="input-group-btn search-form-buttons" style="width: 0px;">' . $buttonsContent . '</div>'
                                 . '</div>';
                    $buttonsRendered = true;
                } else {
                    $formContent .= '<div class="input-group col-md-' . $col . '">' . $this->getView()->formElement($element) . '</div>';
                }
            }
        }

        if (!$buttonsRendered) {
            $c = count($form);
            $col = isset($colMap[$c]) ? $colMap[$c]: 12;
            $formContent .= '<div class="input-group search-form-buttons col-md-' . $col . ' text-right">'
                          . '<div class="btn-group">' . $buttonsContent .'</div></div>';
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Form\View\Helper;

use Zend\Form\View\Helper\Form as ZendForm;
use Zend\Form\FormInterface;
use Zend\Form\FieldsetInterface;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Core\Form\Element\ViewHelperProviderInterface;
use Core\Form\DescriptionAwareFormInterface;

class FormSimple extends ZendForm
{
    public function __invoke(FormInterface $form = null, $parameter = array())
    {
        if (!$form) {
            return $this;
        }
        return $this->render($form, $parameter);
    }

    public function render(FormInterface $form, $parameter = array())
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }
        $formContent = '';
        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface) {
                $formContent.= $this->getView()->formCollection($element);
            } else {
                $formContent.= $this->getView()->formRowSimple($element);
            }
        }

        return $this->openTag($form) . $formContent . $this->closeTag();
    }
}

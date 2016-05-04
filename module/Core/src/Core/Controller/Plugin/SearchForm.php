<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller\Plugin;

use Core\Form\TextSearchForm;
use Zend\Form\FormElementManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class SearchForm extends AbstractPlugin
{

    protected $formElementManager;

    public function __construct(FormElementManager $forms)
    {
        $this->formElementManager = $forms;
    }



    public function __invoke($elementsFieldset, $buttonsFieldset = null)
    {
        return $this->get($elementsFieldset, $buttonsFieldset);
    }

    public function get($elementsFieldset, $buttonsFieldset = null)
    {
        if (is_array($elementsFieldset)) {
            $elementsOptions = isset($elementsFieldset[1]) ? $elementsFieldset[1] : [];
            $elementsFieldset = $elementsFieldset[0];

        } else {
            $elementsOptions = [];
        }

        $form             = $this->formElementManager->get($elementsFieldset, $elementsOptions);
        $params           = $this->getController()->getRequest()->getQuery()->toArray();

        if (!$form instanceOf TextSearchForm) {

            $options = [
                'elements_fieldset' => $form,
            ];
            if (null !== $buttonsFieldset) {
                $options['buttons_fieldset'] = $buttonsFieldset;
            }

            $form = $this->formElementManager->get('Core/TextSearch', $options);
        }

        $form->setSearchParams($params);
        return $form;
    }
}
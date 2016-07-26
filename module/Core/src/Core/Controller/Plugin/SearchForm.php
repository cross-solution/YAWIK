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
 * Fetches a text search form.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
class SearchForm extends AbstractPlugin
{

    /**
     * The form element manager.
     *
     * @var \Zend\Form\FormElementManager
     */
    protected $formElementManager;

    /**
     * Creates an instance.
     *
     * @param FormElementManager $forms
     */
    public function __construct(FormElementManager $forms)
    {
        $this->formElementManager = $forms;
    }

    /**
     * Direct invokation.
     *
     * Proxies to {@link get()}
     *
     * @param string|array     $elementsFieldset
     * @param null|string $buttonsFieldset
     *
     * @return \Core\Form\TextSearchForm
     */
    public function __invoke($elementsFieldset, $buttonsFieldset = null)
    {
        return $this->get($elementsFieldset, $buttonsFieldset);
    }

    /**
     * Fetches a text search form.
     *
     * If only the service for an element fieldset ist passed,
     * it will fetch a "Core/TextSearch" form and pass the
     * elements fieldset along.
     *
     * @param string|array     $elementsFieldset
     * @param string|null $buttonsFieldset
     *
     * @return \Core\Form\TextSearchForm
     */
    public function get($elementsFieldset, $buttonsFieldset = null)
    {
        if (is_array($elementsFieldset)) {
            $elementsOptions = isset($elementsFieldset[1]) ? $elementsFieldset[1] : [];
            $elementsFieldset = $elementsFieldset[0];

        } else {
            $elementsOptions = [];
        }

        $form             = $this->formElementManager->get($elementsFieldset, $elementsOptions);
        /** @noinspection PhpUndefinedMethodInspection */
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
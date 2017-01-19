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
    public function __invoke($form, $options = null)
    {
        return $this->get($form, $options);
    }

    /**
     * Fetches a text search form.
     *
     * If only the service for an element fieldset ist passed,
     * it will fetch a "Core/TextSearch" form and pass the
     * elements fieldset along.
     *
     * @param string|array     $form
     *
     * @return \Core\Form\SearchForm
     */
    public function get($form, $options = null)
    {
        if (!is_object($form)) {
            $form             = $this->formElementManager->get($form, $options);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $params           = $this->getController()->getRequest()->getQuery()->toArray();

        $form->setSearchParams($params);
        return $form;
    }
}
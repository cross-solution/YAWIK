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

use Zend\Form\Form;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\Parameters;

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
     * @var FormElementManager
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
    public function __invoke($form, $options = null, $params = null)
    {
        return $this->get($form, $options, $params);
    }

    /**
     * Fetches a text search form.
     *
     * If only the service for an element fieldset ist passed,
     * it will fetch a "Core/TextSearch" form and pass the
     * elements fieldset along.
     *
     * @param string|Form     $form
     * @param array|null $options
     * @param Parameters $params
     *
     * @return \Core\Form\SearchForm
     */
    public function get($form, $options = null, $params = null)
    {
        if (!is_object($form)) {
            $form             = $this->formElementManager->get($form, $options);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $params           = $params ?: clone $this->getController()->getRequest()->getQuery();

        /* I tried using form methods (bind, isValid)...
         * but because the search form could be in an invalidated state
         * when the page is loaded, we need to hydrate the params manually.
         */
        $hydrator = $form->getHydrator();
        $data     = $hydrator->extract($params);
        $form->setData($data);

        $hydrator->hydrate($data, $params);

        return $form;
    }
}

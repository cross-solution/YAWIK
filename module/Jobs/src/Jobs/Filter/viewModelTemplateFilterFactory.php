<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Filter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Form\Element;
use Core\Entity\EntityInterface;

/**
 * create a viewmodel for the viewmodel, either with tinyMC or rendered content
 * the Factory has to make a choice
 * @param $element
 */
class viewModelTemplateFilterFactory implements FactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->service = $serviceLocator;
        return $this;
    }

    /**
     * @param $element
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     */
    public function __invoke($element)
    {
        $filter = null;
        if ($element instanceof EntityInterface) {
            $filter = new viewModelTemplateFilterJob;
        }
        if ($element instanceof Element) {
            $filter = new viewModelTemplateFilterForm;
            $viewHelperManager = $this->service->get('ViewHelperManager');
            $viewHelperForm = $viewHelperManager->get('formsimple');
            $filter->setViewHelperForm($viewHelperForm);
        }
        if (!isset($filter)) {
            throw new \InvalidArgumentException(get_class($element) . ' cannot be used to initialize a template');
        }
        $viewManager = $this->service->get('viewHelperManager');
        $basePathHelper = $viewManager->get('basePath');
        $serverUrlHelper = $viewManager->get('serverUrl');
        $filter->setBasePathHelper($basePathHelper);
        $filter->setServerUrlHelper($serverUrlHelper);

        $urlPlugin = $this->service->get('controllerPluginManager')->get('url');
        $filter->setUrlPlugin($urlPlugin);
        $options = $this->service->get('Jobs/Options');
        $filter->setConfig($options);
        return $filter->filter($element);
    }
}

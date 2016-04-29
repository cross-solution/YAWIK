<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Filter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Form\Element;
use Jobs\Filter\ViewModelTemplateFilterForm;
use Jobs\Filter\ViewModelTemplateFilterJob;
use Core\Entity\EntityInterface;

/**
 * create a ViewModel for the ViewModel, either with tinyMC or rendered content
 * the Factory has to make a choice
 *
 * @param $element
 */
class ViewModelTemplateFilterFactory implements FactoryInterface
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
            $filter = new ViewModelTemplateFilterJob;
        }
        if ($element instanceof Element) {
            $filter = new ViewModelTemplateFilterForm;
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
        $filter->setTranslator($this->service->get('Translator'));
        return $filter->filter($element);
    }
}

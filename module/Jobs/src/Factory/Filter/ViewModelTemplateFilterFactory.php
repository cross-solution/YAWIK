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

use Interop\Container\ContainerInterface;
use Jobs\View\Helper\JsonLd;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Form\Element;
use Jobs\Filter\ViewModelTemplateFilterForm;
use Jobs\Filter\ViewModelTemplateFilterJob;
use Core\Entity\EntityInterface;

/**
 * create a ViewModel for the ViewModel, either with tinyMC or rendered content
 * the Factory has to make a choice
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author fedys
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @param $element
 */
class ViewModelTemplateFilterFactory implements FactoryInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->service = $container;
        return $this;
    }
    
    /**
     * @param $element
     * @return \Zend\View\Model\ViewModel
     * @throws \InvalidArgumentException
     * @TODO: [ZF3] renamed this method into getModel because conflict with FactoryInterface::__invoke() method
     */
    public function getModel($element)
    {
        $filter = null;
        if ($element instanceof EntityInterface) {
            $filter = new ViewModelTemplateFilterJob;
        }
        if ($element instanceof Element) {
            $filter = new ViewModelTemplateFilterForm;
            $viewHelperManager = $this->service->get('ViewHelperManager');
            $viewHelperForm = $viewHelperManager->get('formSimple');
            $filter->setViewHelperForm($viewHelperForm);
        }
        if (!isset($filter)) {
            throw new \InvalidArgumentException(get_class($element) . ' cannot be used to initialize a template');
        }
        $viewManager = $this->service->get('ViewHelperManager');
        $basePathHelper = $viewManager->get('basePath');
        $serverUrlHelper = $viewManager->get('serverUrl');
        $imageFileCacheHelper = $this->service->get('Organizations\ImageFileCache\Manager');
        $filter->setBasePathHelper($basePathHelper);
        $filter->setImageFileCacheHelper($imageFileCacheHelper);
        $filter->setServerUrlHelper($serverUrlHelper);
        
        if ($filter instanceof ViewModelTemplateFilterJob || method_exists($filter, 'setJsonLdHelper')) {
            $jsonLdHelper = $viewManager->get(JsonLd::class);
            $filter->setJsonLdHelper($jsonLdHelper);
        }
        $urlPlugin = $this->service->get('ControllerPluginManager')->get('url');
        $filter->setUrlPlugin($urlPlugin);
        $options = $this->service->get('Jobs/Options');
        $filter->setConfig($options);
        return $filter->filter($element);
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Filter\HtmlAbsPathFilter;

class HtmlAbsPathFilterFactory implements FactoryInterface
{
    /**
     * Create an HtmlAbsPathFilter filter
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return HtmlAbsPathFilter
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Zend\ServiceManager\AbstractPluginManager $serviceLocator */
        $request = $container->get('request');
        $uri = $request->getUri();
        $filter = new HtmlAbsPathFilter();
        $filter->setUri($uri);
        return $filter;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), HtmlAbsPathFilter::class);
    }
}

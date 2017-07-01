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
use Zend\ServiceManager\Factory\FactoryInterface;
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
}

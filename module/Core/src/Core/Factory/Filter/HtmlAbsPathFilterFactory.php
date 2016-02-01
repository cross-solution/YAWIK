<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory\Filter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Filter\HtmlAbsPathFilter;

class HtmlAbsPathFilterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Zend\ServiceManager\AbstractPluginManager $serviceLocator */
        $request = $serviceLocator->getServiceLocator()->get('request');
        $uri = $request->getUri();
        $filter = new HtmlAbsPathFilter();
        $filter->setUri($uri);
        return $filter;
    }
}

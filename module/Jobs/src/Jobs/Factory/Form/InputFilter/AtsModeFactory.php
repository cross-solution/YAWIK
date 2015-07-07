<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Form\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Jobs\Form\InputFilter\AtsMode;

class AtsModeFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $request = $serviceLocator->getServiceLocator()->get('request');
        $uri = $request->getUri();
        $host = $uri->getHost();
        $filter = new AtsMode();
        $filter->setHost($host);
        return $filter;
    }
} 
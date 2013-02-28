<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Service;


use Zend\ServiceManager\FactoryInterface;
use Auth\Adapter\HybridAuth as HybridAuthAdapter;

/**
 *
 */
class HybridAuthAdapterFactory implements FactoryInterface 
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = new HybridAuthAdapter();
        $adapter->setHybridAuth($serviceLocator->get('HybridAuth'));
        return $adapter;
    }
    
}
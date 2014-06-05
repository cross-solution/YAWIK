<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ServiceLocatorAwareConfiguration.php */ 
namespace Core\Repository\DoctrineMongoODM;

use Doctrine\ODM\MongoDB\Configuration;

class ServiceLocatorAwareConfiguration extends Configuration
{
    
    public function setServiceLocator($serviceLocator)
    {
        $this->attributes['serviceLocator'] = $serviceLocator;
        return $this;
    }
    
    public function getServiceLocator()
    {
        return $this->attributes['serviceLocator'];
    }
}


<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb service */
namespace Applications\Repository\Mapper;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * User mapper factory
 */
class AbstractMapperFactory implements AbstractFactoryInterface
{
    
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return 0 === strpos($requestedName, 'application');
    }
    
    
    /**
     * Creates an instance of \Auth\Mapper\MongoDb\UserMapper.
     * 
     * - Injects an instance of \Auth\Model\User as Prototype
     * - Injects the collection "users" as \MongoCollection-Object
     *   (fetched from the MongoDb service)
     *   
     * @param ServiceLocatorInterface $serviceLocator
     * @return UserMapper
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
        $collection = $db->applications;
        
        if ('application' != $name) {
            $name = substr($name, 11);
        }
        
        $mapperClass = __NAMESPACE__ . '\\' . ucfirst($name) . "Mapper";
        
        $mapper = new $mapperClass($collection);

        return $mapper;
    }
    
}



    
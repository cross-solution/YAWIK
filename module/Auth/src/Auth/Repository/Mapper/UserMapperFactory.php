<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb service */
namespace Auth\Repository\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Repository\Mapper\UserMapper;

/**
 * User mapper factory
 */
class UserMapperFactory implements FactoryInterface
{
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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
        $collection = $db->users;
        
        $mapper = new UserMapper($collection);
        
//         $allowOverride = $serviceLocator->getAllowOverride();
//         $serviceLocator->setAllowOverride(true);
//         $serviceLocator->setService('ApplicationMapper', $mapper);
//         $serviceLocator->setAllowOverride($allowOverride);
//         $mapper->setEducationMapper($serviceLocator->get('EducationMapper'));
        return $mapper;
    }
    
}



    
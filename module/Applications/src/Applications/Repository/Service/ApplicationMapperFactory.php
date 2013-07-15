<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb service */
namespace Applications\Repository\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Applications\Repository\Mapper\ApplicationMapper;

/**
 * User mapper factory
 */
class ApplicationMapperFactory implements FactoryInterface
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
        $db = $serviceLocator->get('MongoDb');
        $collection = $db->applications;
        
        $mapper = new ApplicationMapper($collection);
        
//         $allowOverride = $serviceLocator->getAllowOverride();
//         $serviceLocator->setAllowOverride(true);
//         $serviceLocator->setService('ApplicationMapper', $mapper);
//         $serviceLocator->setAllowOverride($allowOverride);
//         $mapper->setEducationMapper($serviceLocator->get('EducationMapper'));
        return $mapper;
    }
    
}



    
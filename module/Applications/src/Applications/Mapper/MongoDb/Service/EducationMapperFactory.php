<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb service */
namespace Applications\Mapper\MongoDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Applications\Mapper\MongoDb\ApplicationMapper;
use Applications\Mapper\MongoDb\EducationMapper;
use Applications\Model\Education;


/**
 * User mapper factory
 */
class EducationMapperFactory implements FactoryInterface
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
        $mapper = new EducationMapper();
        $mapper->setModelPrototype(new Education());
        $db = $serviceLocator->get('MongoDb');
        $mapper->setCollection($db->applications);
        return $mapper;
    }
}



    
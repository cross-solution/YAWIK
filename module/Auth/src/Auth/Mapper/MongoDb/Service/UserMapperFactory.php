<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Mapper\MongoDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Mapper\MongoDb\UserMapper;
use Auth\Model\UserModel;

class UserMapperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mapper = new UserMapper();
        $mapper->setModelPrototype(new UserModel());
        $db = $serviceLocator->get('MongoDb');
        $mapper->setCollection($db->users);
        return $mapper;
    }
}


    
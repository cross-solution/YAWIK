<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileRepositoryAbstractFactory.php */ 
namespace Core\Repository\Mapper;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileMapperAbstractFactory implements AbstractFactoryInterface
{
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractFactoryInterface::canCreateServiceWithName()
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) 
    {
        return '/files' == strtolower(substr($requestedName, -6));
    }
 
 
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
    */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) 
    {
        list($fileStore,) = explode('/', $requestedName);
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
        $grid = new \MongoGridFS($db, strtolower($fileStore));
        $builder = $serviceLocator->getServiceLocator()->get('builders')->get('Core/File');
        
        $mapper = new FileMapper($grid, $builder);
        
        
        return $mapper;
    }
}
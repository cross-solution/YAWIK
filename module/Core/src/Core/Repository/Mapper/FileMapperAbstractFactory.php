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
        list($fileStore,) = explode('/', strtolower($requestedName));
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
        $grid = new \MongoGridFS($db, $fileStore);
        $builders = $serviceLocator->getServiceLocator()->get('builders');
        $builder = $builders->canCreate("$fileStore/File")
                 ? $builders->get("$fileStore/File")
                 : $builders->get('Core/File');
        $builder->setFileStoreName($fileStore);
        $mapper = new FileMapper($grid, $builder);
        
        
        return $mapper;
    }
}
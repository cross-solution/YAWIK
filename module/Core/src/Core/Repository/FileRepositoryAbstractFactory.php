<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** FileRepositoryAbstractFactory.php */ 
namespace Core\Repository;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FileRepositoryAbstractFactory implements AbstractFactoryInterface
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
        $mapper = $serviceLocator->getServiceLocator()->get('mappers')->get($requestedName);
        
        $repository = new FileRepository($mapper);
        return $repository;
    }
}
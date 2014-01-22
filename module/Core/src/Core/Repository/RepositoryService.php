<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** RepositoryService.php */ 
namespace Core\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class RepositoryService extends ServiceManager implements ServiceLocatorAwareInterface
{
    protected $services;
    protected $dm;

    public function getServiceLocator()
    {
        return $this->services;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
        return $this;
    }
    
    protected function getDocumentManager()
    {
        if (!$this->dm) {
            $this->dm = $this->services->get('doctrine.documentmanager.odm_default');
        }
        return $this->dm;
    }
    
    
    public function get($name)
    {
        $nameParts = explode('/', $name);
        if (2 > count($nameParts)) {
            throw new \InvalidArgumentException('Name must be in the format "Namespace/Entity")');
        }
        
        $repository = $this->has($name) ? parent::get($name) : new GenericRepository();
        
        if (!$repository instanceOf AbstractRepository) {
            throw new \DomainException('Repository must implement \Core\Repository\AbstractRepository');
        } 
        
        $namespace   = $nameParts[0];
        $entityName  = $nameParts[1];
        $entityClass = "\\$namespace\\Entity\\$entityName";
        
        if (!class_exists($entityClass)) {
            throw new \DomainException(sprintf('Entity %s does not exist.', $entityClass));
        }
        
        $doctrineRepository = $this->getDocumentManager()->getRepository($entityClass);
        $repository->setEntityPrototype(new $entityClass());
        $repository->setDocumentRepository($doctrineRepository);
        
        return $repository;
    }
    
    public function createQueryBuilder()
    {
        return $this->getDocumentManager()->createQueryBuilder();
    }
    
    public function store(EntityInterface $entity)
    {
        $dm = $this->getDocumentManager();
        $dm->persist($entity);
        $dm->flush();
    }
    
    public function remove(EntityInterface $entity)
    {
        $dm = $this->getDocumentManager();
        $dm->remove($entity);
        $dm->flush();
    }
    
    
}


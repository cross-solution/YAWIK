<?php

namespace Core\Repository;


use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB as ODM;
use Zend\ServiceManager\ServiceLocatorInterface;



abstract class AbstractRepository extends ODM\DocumentRepository implements RepositoryInterface
{

    protected $entityPrototype;
    
    public function __construct(ODM\DocumentManager $dm, ODM\UnitOfWork $uow, ODM\Mapping\ClassMetadata $class)
    {
        parent::__construct($dm, $uow, $class);
        $eventArgs = new DoctrineMongoODM\Event\EventArgs(array(
            'repository' => $this
        ));
        $dm->getEventManager()->dispatchEvent(DoctrineMongoODM\Event\RepositoryEventsSubscriber::postConstruct, $eventArgs);
    }
    
    public function init(ServiceLocatorInterface $serviceLocator)
    {
        
    }

    public function getService($name)
    {
        return $this->dm->getConfiguration()->getServiceLocator()->get($name);
    }
    
    public function setEntityPrototype(EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }

    public function create(array $data=null) {
        if (null === $this->entityPrototype) {
            throw new \RuntimeException('Could not create an entity. No protoype is set!');
        }

        $entity = clone $this->entityPrototype;
        
        if (null !== $data) {
            foreach ($data as $property => $value) {
                $entity->$property = $value;
            }
        }
        
        return $entity;
    }

}
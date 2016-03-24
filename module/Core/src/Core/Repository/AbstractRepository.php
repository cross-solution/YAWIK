<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;
use \Doctrine\ODM\MongoDB as ODM;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractRepository extends ODM\DocumentRepository implements RepositoryInterface
{

    protected $entityPrototype;

    /**
     * @param ODM\DocumentManager       $dm
     * @param ODM\UnitOfWork            $uow
     * @param ODM\Mapping\ClassMetadata $class
     */
    public function __construct(ODM\DocumentManager $dm, ODM\UnitOfWork $uow, ODM\Mapping\ClassMetadata $class)
    {
        parent::__construct($dm, $uow, $class);
        $eventArgs = new DoctrineMongoODM\Event\EventArgs(
            array(
            'repository' => $this
            )
        );
        $dm->getEventManager()->dispatchEvent(DoctrineMongoODM\Event\RepositoryEventsSubscriber::postConstruct, $eventArgs);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function init(ServiceLocatorInterface $serviceLocator)
    {
        
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getService($name)
    {
        return $this->dm->getConfiguration()->getServiceLocator()->get($name);
    }

    /**
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function setEntityPrototype(EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data = null)
    {
        if (null === $this->entityPrototype) {
            throw new \RuntimeException('Could not create an entity. No prototype is set!');
        }

        $entity = clone $this->entityPrototype;
        
        if (null !== $data) {
            foreach ($data as $property => $value) {
                $setter = "set$property";

                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }
        }
        
        return $entity;
    }

    /**
     * @param $entity
     * @throws \InvalidArgumentException
     * @return self
     */
    public function store($entity)
    {
        if ( !($entity instanceOf $this->entityPrototype) ) {
            throw new \InvalidArgumentException(sprintf(
                'Entity must be of type %s but recieved %s instead',
                get_class($this->entityPrototype),
                get_class($entity)
            ));
        }

        $this->dm->persist($entity);
        $this->dm->flush($entity);

        return $this;
    }
}

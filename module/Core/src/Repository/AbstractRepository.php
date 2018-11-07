<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;
use Doctrine\ODM\MongoDB as ODM;
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
        /* @TODO: [ZF3] ->getServiceLocator() should be removed in future */
        $config = $this->dm->getConfiguration();
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
    public function create(array $data = null, $persist = false)
    {
        if (null === $this->entityPrototype) {
            throw new \RuntimeException('Could not create an entity. No prototype is set!');
        }

        $entity = clone $this->entityPrototype;
        
        $eventArgs = new DoctrineMongoODM\Event\EventArgs([
            'entity' => $entity
        ]);
        $this->dm->getEventManager()
            ->dispatchEvent(DoctrineMongoODM\Event\RepositoryEventsSubscriber::postCreate, $eventArgs);
        
        if (null !== $data) {
            foreach ($data as $property => $value) {
                $setter = "set$property";

                if (method_exists($entity, $setter)) {
                    $entity->$setter($value);
                }
            }
        }

        if ($persist) {
            $this->dm->persist($entity);
        }

        return $entity;
    }

    public function count(array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        foreach ($criteria as $field => $value) {
            $qb->field($field)->equals($value);
        }
        $qb->count();
        $q = $qb->getQuery();
        $r = $q->execute();

        return $r;
    }

    /**
     * @param $entity
     * @throws \InvalidArgumentException
     * @return self
     */
    public function store($entity)
    {
        $this->checkEntityType($entity);
        $this->dm->persist($entity);
        $this->dm->flush($entity);

        return $this;
    }

    public function remove($entity, $flush=false)
    {
        $this->checkEntityType($entity);
        $this->dm->remove($entity);
        if ($flush) {
            $this->dm->flush($entity);
        }
        return $this;
    }

    protected function checkEntityType($entity)
    {
        if (!($entity instanceof $this->entityPrototype)) {
            throw new \InvalidArgumentException(sprintf(
                                                    'Entity must be of type %s but recieved %s instead',
                                                    get_class($this->entityPrototype),
                                                    get_class($entity)
                                                ));
        }
    }
}

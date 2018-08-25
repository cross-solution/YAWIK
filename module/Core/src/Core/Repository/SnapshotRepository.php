<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Repository;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\SnapshotAttributesProviderInterface;
use Core\Entity\SnapshotInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Zend\Hydrator\HydratorInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class SnapshotRepository extends DocumentRepository
{
    /**
     *
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     *
     *
     * @var HydratorInterface
     */
    protected $sourceHydrator;

    /**
     *
     *
     * @var array
     */
    protected $snapshotAttributes = [];

    /**
     * @param \Zend\Hydrator\HydratorInterface $hydrator
     *
     * @return self
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->setHydrator(new EntityHydrator());
        }
        return $this->hydrator;
    }

    /**
     * @param \Zend\Hydrator\HydratorInterface $sourceHydrator
     *
     * @return self
     */
    public function setSourceHydrator($sourceHydrator)
    {
        $this->sourceHydrator = $sourceHydrator;

        return $this;
    }

    /**
     * @return \Zend\Hydrator\HydratorInterface
     */
    public function getSourceHydrator()
    {
        if (!$this->sourceHydrator) {
            $this->sourceHydrator = $this->getHydrator();
        }

        return $this->sourceHydrator;
    }

    /**
     * @param array $snapshotAttributes
     *
     * @return self
     */
    public function setSnapshotAttributes($snapshotAttributes)
    {
        $this->snapshotAttributes = $snapshotAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getSnapshotAttributes()
    {
        return $this->snapshotAttributes;
    }

    public function create(EntityInterface $source, $persist = true)
    {
        $snapshot = $this->getDocumentName();
        $snapshot = new $snapshot($source);

        $eventArgs = new DoctrineMongoODM\Event\EventArgs([
            'entity' => $snapshot
        ]);
        $this->dm->getEventManager()
                 ->dispatchEvent(DoctrineMongoODM\Event\RepositoryEventsSubscriber::postCreate, $eventArgs);

        $this->copy($source, $snapshot);

        if ($persist) {
            $this->store($snapshot);
        }

        return $snapshot;
    }

    protected function copy($source, $target, $inverse = false)
    {
        if ($inverse) {
            $attributes = $this->getCopyAttributes($target, $source);
            $sourceHydrator = $this->getHydrator();
            $targetHydrator = $this->getSourceHydrator();
        } else {
            $attributes = $this->getCopyAttributes($source, $target);
            $sourceHydrator = $this->getSourceHydrator();
            $targetHydrator = $this->getHydrator();
            $source = clone $source;
        }


        $data = $sourceHydrator->extract($source);
        $data = array_intersect_key($data, array_flip($attributes));
        $targetHydrator->hydrate($data, $target);
    }

    protected function getCopyAttributes($source, $target)
    {
        $attributes = $this->getSnapshotAttributes();

        if (!empty($attributes)) {
            return $attributes;
        }

        if ($source instanceof SnapshotAttributesProviderInterface) {
            return $source->getSnapshotAttributes();
        }

        if ($target instanceof SnapshotAttributesProviderInterface) {
            return $target->getSnapshotAttributes();
        }

        return [];
    }

    public function merge(SnapshotInterface $snapshot, $snapshotDraftStatus = false)
    {
        $this->checkEntityType($snapshot);

        $meta       = $snapshot->getSnapshotMeta();
        $entity     = $snapshot->getOriginalEntity();

        $meta->setIsDraft((bool) $snapshotDraftStatus);

        $this->copy($snapshot, $entity, true);

        return $entity;
    }

    /**
     * @param SnapshotInterface $snapshot
     * @todo implement or remove this method
     */
    public function diff(SnapshotInterface $snapshot)
    {
        $entity = $snapshot->getOriginalEntity();
        $attributes = $this->getCopyAttributes($entity, $snapshot);
    }

    public function findLatest($sourceId, $isDraft = false)
    {
        $entity = $this->createQueryBuilder()
          ->field('snapshotEntity')->equals(new \MongoId($sourceId))
          ->field('snapshotMeta.isDraft')->equals($isDraft)
          ->sort('snapshotMeta.dateCreated.date', 'desc')
          ->limit(1)
          ->getQuery()
          ->getSingleResult()
        ;
        if ($entity) {
            $this->dm->getEventManager()->dispatchEvent(
                \Doctrine\ODM\MongoDB\Events::postLoad,
                new \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs($entity, $this->dm)
            );
        }

        return $entity;
    }

    public function findBySourceId($sourceId, $includeDrafts = false)
    {
        $criteria = ['snapshotEntity' => $sourceId];

        if (!$includeDrafts) {
            $criteria['snapshotMeta.isDraft'] = false;
        }

        return $this->findBy($criteria);
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

    public function remove($entity)
    {
        $this->checkEntityType($entity);
        $this->dm->remove($entity);

        return $this;
    }

    /**
     * @param $sourceId
     * @todo implement or remove this method
     */
    public function removeAll($sourceId)
    {
        throw new \LogicException("This method is not implemented yet");
    }

    protected function checkEntityType($entity)
    {
        if (!is_a($entity, $this->getDocumentName())) {
            throw new \InvalidArgumentException(sprintf(
                'Entity must be of type %s but received %s instead',
                $this->getDocumentName(),
                get_class($entity)
            ));
        }
    }

    /**
     * @param $source
     * @param array $attributes
     * @return array
     * @todo remove this method if not used, because extract already implemented in $this->sourceHydrator->extract
     */
    protected function extract($source, array $attributes = [])
    {
        $hydrator = $this->getSourceHydrator();
        $data     = $hydrator->extract($source);
        $hydrate  = [];

        if (empty($attributes)) {
            $attributes = array_keys($data);
        }

        foreach ($attributes as $key => $spec) {
            if (is_numeric($key)) {
                $key = $spec;
                $spec = null;
            }

            if ($data[$key] instanceof EntityInterface) {
                $hydrate[$key] = clone $data[$key];
            } elseif ($data[$key] instanceof Collection) {
                $collection = new ArrayCollection();
                foreach ($data[$key] as $item) {
                    $collection->add(clone $item);
                }
                $hydrate[$key] = $collection;
            } else {
                $hydrate[$key] = $data[$key];
            }
        }

        return $hydrate;
    }
}

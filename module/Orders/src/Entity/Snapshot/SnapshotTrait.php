<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity\Snapshot;

use Core\Entity\EntityInterface;
use Core\Exception\ImmutablePropertyException;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Proxy\Proxy;


/**
 * Implementation of SnapshotInterface.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait SnapshotTrait
{

    /**
     * The original entity.
     *
     * @ODM\ReferenceOne(discriminatorField="_entity")
     * @var EntityInterface
     */
    protected $entity;

    public function setEntity(EntityInterface $entity)
    {
        if (null !== $this->entity) {
            throw new ImmutablePropertyException('entity', $this);
        }

        $this->entity = $entity;

        return $this;
    }

    public function getEntity()
    {
        return $this->hasEntity() ? $this->entity : null;
    }

    public function hasEntity()
    {
        $entity = $this->getEntity();

        if (!$entity instanceOf Proxy) {
            return $entity instanceOf EntityInterface;
        }

        if ($entity->__isInitialized()) {
            return true;
        }

        try {
            $entity->__load();
            return true;

        } catch (DocumentNotFoundException $ex) {
            return false;
        }
    }
    
}
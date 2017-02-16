<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Exception\ImmutablePropertyException;

/**
 * Class SnapshotMeta
 *
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks
 */
class SnapshotMeta implements ModificationDateAwareEntityInterface, DraftableEntityInterface
{
    use ModificationDateAwareEntityTrait, DraftableEntityTrait;

    /**
     * @var EntityInterface
     * @ODM\ReferenceOne(discriminatorField="_entity", storeAs="dbRef")
     */
    protected $entity;

    /**
     * Sets the entity
     *
     * @param $entity
     * @throws \Core\Exception\ImmutablePropertyException
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Gets the Entity
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

}

<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Exception\ImmutablePropertyException;

/**
 * Class SnapshotMeta
 * @package Core\Entity
 *
 * @ODM\MappedSuperclass
 */
class SnapshotMeta extends AbstractIdentifiableModificationDateAwareEntity {

    /**
     * Entity id
     *
     * @var mixed
     * @ODM\Id
     */
    protected $id;

    /**
    * @var EntityInterface
    * @ODM\EmbedOne
    * @ODM\Index
     */
    protected $entity;

    /**
     * @var string
     * @ODM\String
     */
    protected $sourceId;

    /**
     * @param $entity
     * @return $this
     * @throws \Core\Exception\ImmutablePropertyException
     */
    public function setEntity($entity)
    {
        if (isset($this->entity)) {
            throw new ImmutablePropertyException('setEntity', $this);
        }
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $id
     */
    public function setSourceId($id)
    {
        $this->sourceId = $id;
    }

} 
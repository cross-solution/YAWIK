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
 * Class JobSnapshotMeta
 * @package Jobs\Entity
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
    * @var Entity
    * @ODM\EmbedOne
    * @ODM\Index
     */
    protected $entity;

    /**
     * @var string
     * @ODM\String
     */
    protected $sourceId;

    public function setEntity($entity)
    {
        if (isset($this->entity)) {
            throw new ImmutablePropertyException('setEntity', $this);
        }
        $this->entity = $entity;
        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setSourceId($id)
    {
        $this->sourceId = $id;
    }

} 
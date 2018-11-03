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
use Core\Entity\EntityInterface;

/**
 * Class Snapshot
 * @package Core\Entity
 *
 * @ODM\MappedSuperclass
 */
abstract class Snapshot extends AbstractIdentifiableModificationDateAwareEntity implements SnapshotInterface
{
    /**
     * @param $data
     * @return mixed
     * @ODM\PreUpdate
     */
    public function __invoke($data)
    {
        foreach ($data as $key => $attribute) {
            $this->$key = $attribute;
        }
        return $this;
    }
}

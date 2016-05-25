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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface SnapshotInterface 
{

    /**
     * Sets the reference to the original entity.
     *
     * @param EntityInterface $entity
     *
     * @return self
     * @throws \Core\Exception\ImmutablePropertyException if entity was already set.
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Gets the reference to the original entity.
     *
     * If the original entity does not exists anymore,
     * <b>null</b> is returned.
     *
     * @return EntityInterface|null
     */
    public function getEntity();

    /**
     * Does the original entity still exists?
     *
     * @return bool
     */
    public function hasEntity();
}
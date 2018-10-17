<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

use Core\Entity\Exception\ImmutableEntityException;
use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Implementation of ImmutableEntityInterface.
 *
 * Do not forget to add the \@HasLifecycleEvents tag to the entity class.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
trait ImmutableEntityTrait
{
    /**
     * This method throws an exception whenever the entity is about to be updated.
     *
     * @ODM\PreUpdate
     * @throws \Core\Entity\Exception\ImmutableEntityException
     */
    public function preventUpdate()
    {
        throw new ImmutableEntityException($this);
    }
}

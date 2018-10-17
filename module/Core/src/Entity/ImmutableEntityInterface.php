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

/**
 * Interface for an immutable entity.
 *
 * At the moment this means, the implementing class must add the
 * preventUpdate method with a \@PreUpdate hook, that throws an exception.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
interface ImmutableEntityInterface
{
    /**
     * This method should throw an exception whenever the entity is about to be updated.
     *
     * @throws \Core\Entity\Exception\ImmutableEntityException
     */
    public function preventUpdate();
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Exception;

/**
 * Exception for indicating that an immutable entity is about to be updated.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.23
 */
class ImmutableEntityException extends \RuntimeException implements ExceptionInterface
{
    /**
     * Creates an ImmutableEntityException.
     *
     * @param \Core\Entity\EntityInterface|string $entity Entity class or class name.
     */
    public function __construct($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        $message = sprintf('%s is an immutable entity.', $entity);

        parent::__construct($message);
    }
}

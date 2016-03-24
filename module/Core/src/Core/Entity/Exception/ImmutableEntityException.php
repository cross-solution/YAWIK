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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
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
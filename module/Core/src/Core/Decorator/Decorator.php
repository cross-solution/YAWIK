<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Decorator;

/**
 * Decorator boilerplate.
 *
 * Allows creation of Decorators for a specific object type and assures it only
 * decorates that type.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class Decorator
{

    /**
     * The wrapped object,
     *
     * @var object
     */
    protected $object;

    /**
     * The type the wrapped object must be of
     *
     * @var string
     */
    protected $objectType = '\stdClass';

    /**
     * Creates an instance.
     *
     * @param object $object the concrete entity to decorate.
     */
    public function __construct($object)
    {
        $this->checkObjectType($object);
        $this->object = $object;
    }

    /**
     * Checks the type of the wrapped entity
     *
     * @param object $object
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function checkObjectType($object)
    {
        if (!$object instanceof $this->objectType) {
            throw new \InvalidArgumentException('Wrapped entity must be of type ' . $this->objectType);
        }
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Exception;

class ImmutablePropertyException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($propertyName, $object, \Exception $previous = null)
    {
        if (is_object($object)) {
            $object = get_class($object);
        }
        $message = sprintf(
            'Property "%s" of class "%s" is immutable.',
            $propertyName,
            $object
        );

        parent::__construct($message, 0, $previous);
    }
}

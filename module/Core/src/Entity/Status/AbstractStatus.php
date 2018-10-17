<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Status;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Abstract status class.
 *
 * To use this boilerplate, it must be extended and all
 * available states must be defined as class constants.
 *
 * <pre>
 * // Do not forget the ODM Annotations!
 *
 * class Status extends AbstractStatus
 * {
 *      const NEW = 'new';
 *      const MODIFIED = 'modified';
 * }
 *
 * $state = new Status(Status::NEW); // or new Status('new');
 * $state->is(Status::NEW) // => true
 * $state->is(new Status(Status::MODIFIED)) // => false
 * </pre>
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
abstract class AbstractStatus implements StatusInterface
{
    /**
     * The state name.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $state;

    public static function getStates()
    {
        $reflection = new \ReflectionClass(static::class);

        return array_values($reflection->getConstants());
    }

    public function __construct($state)
    {
        $states = static::getStates();

        if (!in_array($state, $states)) {
            throw new \InvalidArgumentException('Invalid state name: ' . $state);
        }

        $this->state = $state;
    }

    public function __toString()
    {
        return $this->state;
    }

    public function is($state)
    {
        return $this->state == (string) $state;
    }
}

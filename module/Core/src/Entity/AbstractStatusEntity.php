<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
abstract class AbstractStatusEntity implements StatusInterface
{
    protected static $orderMap = [];

    public static function getStates()
    {
        $map = static::$orderMap;
        asort($map, SORT_NUMERIC);

        return array_keys($map);
    }

    /**
     * The default status to be set if none provided.
     *
     * @var string
     */
    protected $default;

    /**
     * The status name.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    private $name;

    /**
     * The order priority
     *
     * @ODM\Field(type="int")
     * @var int
     */
    private $order;

    public function __construct($name = null)
    {
        $this->init($name);
    }

    public function __toString()
    {
        return $this->name;
    }

    public function is($name)
    {
        return $this->name == (string) $name;
    }

    /**
     * Initialize this state.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    private function init($name)
    {
        if (null === $name) {
            $name = $this->default;
        }

        if (!isset(static::$orderMap[ $name ])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown status name "%s" for "%s"',
                $name,
                static::class
            ));
        }

        $this->name  = $name;
        $this->order = static::$orderMap[ $name ];
    }
}

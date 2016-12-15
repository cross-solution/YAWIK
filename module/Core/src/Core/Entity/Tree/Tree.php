<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Tree;

use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * base class for trees.
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
abstract class Tree implements TreeInterface
{
    use EntityTrait, IdentifiableEntityTrait;

    /**
     * Name of this item.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;

    /**
     * Value of this item.
     *
     * Used in select form elements.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $value;

    /**
     * Order priority.
     *
     * @ODM\Field(type="int")
     * @var int
     */
    protected $priority = 0;

    /**
     * Creates a new Tree item.
     *
     * @param null|string $name
     * @param null|string $value
     * @param int  $priority
     */
    public function __construct($name = null, $value = null, $priority = 0)
    {
        if (null !== $name) {
            $this->setName($name);
            $this->setValue($value);
            $this->setPriority($priority);
        }
    }

    /**
     * Set the name.
     *
     * @param string $name
     *
     * @return self
     * @throws \InvalidArgumentException if $name is empty.
     */
    public function setName($name)
    {
        if (!$name) {
            throw new \InvalidArgumentException('Name must not be empty.');
        }

        $this->name = (string) $name;

        return $this;
    }

    /**
     * get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value.
     *
     * Used in form selects.
     *
     * @param string $value
     *
     * @return self
     * @throws \InvalidArgumentException if $value AND {@link name} are empty.
     */
    public function setValue($value)
    {
        if (!$value) {
            if (!$this->getName()) {
                throw new \InvalidArgumentException('Value must not be empty.');
            }
            $value = strtolower(str_replace(' ', '-', $this->getName()));
        }

        $this->value = (string) $value;

        return $this;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the priority.
     *
     * @param int $priority
     *
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    /**
     * Get the priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}
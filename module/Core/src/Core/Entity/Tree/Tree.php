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
 * ${CARET}
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class Tree implements TreeInterface
{
    use EntityTrait, IdentifiableEntityTrait;

    /**
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;

    /**
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $value;

    /**
     *
     * @ODM\Field(type="int")
     * @var int
     */
    protected $priority = 0;

    public function __construct($name = null, $value = null, $priority = 0)
    {
        if (null !== $name) {
            $this->setName($name);
            $this->setValue($value);
            $this->setPriority($priority);
        }
    }

    /**
     * @param string $name
     *
     * @return self
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     *
     * @return self
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
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
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
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}
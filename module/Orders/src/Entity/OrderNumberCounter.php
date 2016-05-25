<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\ImmutableEntityInterface;
use Core\Entity\ImmutableEntityTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\Document(collection="orders.ordernumbercounter")
 * @ODM\UniqueIndex(keys={"name"=1, "count"=1})
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class OrderNumberCounter implements EntityInterface, IdentifiableEntityInterface, ImmutableEntityInterface
{
    use EntityTrait, IdentifiableEntityTrait, ImmutableEntityTrait;

    /**
     * The name of this counter. used as Prefix for the number.
     *
     * @ODM\String
     * @var string
     */
    protected $name;

    /**
     * the next counter number to use.
     *
     * @ODM\Field(type="int")
     * @var int
     */
    protected $count = 0;

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @param int $count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    public function format($format = null)
    {
        if (null === $format) {
            $format = '%1$s-%2$s';
        }

        return sprintf($format, $this->getName(), $this->getCount());
    }

    public function __toString()
    {
        return $this->format();
    }
}
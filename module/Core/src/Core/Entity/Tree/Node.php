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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use Doctrine\Common\Collections\Collection;
use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * base class for trees.
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class Node implements NodeInterface
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
     * Child nodes.
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", storeAs="dbRef", strategy="set", sort={"priority"="asc"}, cascade="all", orphanRemoval="true")
     * @var Collection
     */
    protected $children;

    /**
     * Parent node.
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", storeAs="dbRef", nullable="true")
     * @var
     */
    protected $parent;

    final public static function filterValue($value)
    {
        $value = mb_strtolower($value);
        $value = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $value);
        $value = preg_replace(['~[^a-z0-9]~', '~__+~'], '_', $value);

        return $value;
    }

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
            $value = self::filterValue($this->getName());
        }

        $this->value = (string) $value;

        return $this;
    }

    public function getValue()
    {
        if (!$this->value) {
            $this->setValue(null);
        }

        return $this->value;
    }

    public function getValueWithParents($withRoot = false, $useNames = false)
    {
        $parts = [ ($useNames ? $this->getName() : $this->getValue()) ];
        $item = $this;

        while ($item = $item->getParent()) {
            $parts[] = $useNames ? $item->getName() : $item->getValue();
        }

        if (!$withRoot) {
            array_pop($parts); // No root node.
        }

        $parts = array_reverse($parts);
        $value = join(($useNames ? ' | ' : '-'), $parts);

        return $value;
    }

    public function getNameWithParents($withRoot = false)
    {
        return $this->getValueWithParents($withRoot, true);
    }

    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setChildren(Collection $children)
    {
        $this->children = $children;

        /* @var NodeInterface $child */
        foreach ($children as $child) {
            $child->setParent($this);
        }

        return $this;
    }

    public function getChildren()
    {
        if (!$this->children) {
            $this->setChildren(new ArrayCollection());
        }

        return $this->children;
    }

    public function hasChildren()
    {
        return (bool) $this->getChildren()->count();
    }

    public function addChild(NodeInterface $child)
    {
        $this->getChildren()->add($child);
        $child->setParent($this);

        return $this;
    }

    public function removeChild(NodeInterface $child)
    {
        $this->getChildren()->removeElement($child);

        return $this;
    }

    public function clearChildren()
    {
        $this->getChildren()->clear();

        return $this;
    }

    public function setParent(NodeInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     *
     *
     * @return NodeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }
}

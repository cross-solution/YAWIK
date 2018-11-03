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

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Tree node interface.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface NodeInterface extends EntityInterface, IdentifiableEntityInterface
{
    /**
     * Set the name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the value.
     *
     * Used in select form elements.
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue($value);

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Get the value prefixed with all the parent values.
     *
     * @param bool $withRoot if true, the root node is also included.
     *
     * @return string
     */
    public function getValueWithParents($withRoot = false, $useNames = false);

    /**
     * Set the order priority.
     *
     * @param int $priority
     *
     * @return self
     */
    public function setPriority($priority);

    /**
     * Get the priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Has this node any children.
     *
     * @internal
     *      This is needed for the {@link \Core\Form\Hydrator\TreeHydrator}
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Get the children of this node.
     *
     * @return Collection|\Traversable|array
     */
    public function getChildren();

    /**
     * Add a child.
     *
     * @param NodeInterface $child
     *
     * @return self
     */
    public function addChild(NodeInterface $child);

    /**
     * Remove a child.
     *
     * @param NodeInterface $child
     *
     * @return self
     */
    public function removeChild(NodeInterface $child);

    /**
     * Clear all children.
     *
     * @return self
     */
    public function clearChildren();

    /**
     * Set parent of this node.
     *
     * @param NodeInterface $parent
     *
     * @return mixed
     */
    public function setParent(NodeInterface $parent);

    /**
     * Get the parent of this node.
     *
     * @return NodeInterface
     */
    public function getParent();
}

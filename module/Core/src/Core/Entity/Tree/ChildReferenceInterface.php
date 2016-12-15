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
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping as ODM;

/**
 * Interface for a tree with child reference strategy.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface ChildReferenceInterface
{

    /**
     * Set the children.
     *
     * @param \Doctrine\Common\Collections\Collection $children
     *
     * @return self
     */
    public function setChildren(Collection $children);

    /**
     * Get the children.
     *
     * @return \Doctrine\Common\Collections\Collection|\Traversable|array
     */
    public function getChildren();

    /**
     * Add a child.
     *
     * @param TreeInterface $child
     *
     * @return self
     */
    public function addChild(TreeInterface $child);

    /**
     * Remove a child.
     *
     * @param TreeInterface $child
     *
     * @return self
     */
    public function removeChild(TreeInterface $child);

    /**
     * Clear all children.
     *
     * @return self
     */
    public function clearChildren();

}
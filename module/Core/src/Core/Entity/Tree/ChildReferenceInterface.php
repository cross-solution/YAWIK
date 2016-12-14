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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface ChildReferenceInterface
{

    /**
     * @param \Doctrine\Common\Collections\Collection $children
     *
     * @return self
     */
    public function setChildren(Collection $children);

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren();

    public function addChild(Tree $child);

    public function removeChild(Tree $child);

    public function clearChildren();

}
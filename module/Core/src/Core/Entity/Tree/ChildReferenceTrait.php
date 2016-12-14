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
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait ChildReferenceTrait
{
    /**
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", strategy="set", sort={"priority"="asc"}, cascade="all", orphanRemoval="true")
     * @var Collection
     */
    protected $children;

    /**
     * @param \Doctrine\Common\Collections\Collection $children
     *
     * @return self
     */
    public function setChildren(Collection $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->getInternalChildren()->toArray();
    }

    public function hasChildren()
    {
        return (bool) $this->getInternalChildren()->count();
    }

    public function addChild(Tree $child)
    {
        $this->getInternalChildren()->add($child);

        return $this;
    }

    public function removeChild(Tree $child)
    {
        $this->getInternalChildren()->removeElement($child);

        return $this;
    }

    public function clearChildren()
    {
        $this->getInternalChildren()->clear();

        return $this;
    }

    private function getInternalChildren()
    {
        if (!$this->children) {
            $this->setChildren(new ArrayCollection());
        }

        return $this->children;
    }
}
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

use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Doctrine\Common\Collections\Collection;

/**
 * ${CARET}
 *
 * @ODM\MappedSuperclass
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class AttachedLeafs implements EntityInterface, IdentifiableEntityInterface
{
    use EntityTrait, IdentifiableEntityTrait;

    /**
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", strategy="set", sort={"priority"="asc"})
     * @var Collection
     */
    private $items;

    public function getItems()
    {
        if (!$this->items) {
            $this->setItems(new ArrayCollection());
        }

        return $this->items;
    }

    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    public function __toString()
    {
        $items = $this->getItems()->toArray();
        $items = array_map(function($i) { /* @var TreeInterface $i */ return $i->getName(); }, $items);
        $items = implode(', ', $items);

        return $items;
    }
}
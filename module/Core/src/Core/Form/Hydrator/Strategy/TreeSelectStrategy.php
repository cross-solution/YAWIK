<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Hydrator\Strategy;

use Core\Entity\Tree\AbstractLeafs;
use Core\Entity\Tree\NodeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class TreeSelectStrategy implements StrategyInterface
{

    private $attachedLeafs;
    private $treeRoot;
    private $allowSelectMultipleItems = false;

    /**
     * @param mixed $attachedLeafs
     *
     * @return self
     */
    public function setAttachedLeafs($attachedLeafs)
    {
        $this->attachedLeafs = $attachedLeafs;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttachedLeafs()
    {
        return $this->attachedLeafs;
    }

    /**
     * @param mixed $treeRoot
     *
     * @return self
     */
    public function setTreeRoot($treeRoot)
    {
        $this->treeRoot = $treeRoot;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTreeRoot()
    {
        return $this->treeRoot;
    }

    /**
     * @param Callable|bool $flagOrCallback
     *
     * @return self
     */
    public function setAllowSelectMultipleItems($flagOrCallback)
    {
        $this->allowSelectMultipleItems = $flagOrCallback;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowSelectMultipleItems()
    {
        $flagOrCallback = $this->allowSelectMultipleItems;

        return is_callable($flagOrCallback) ? (bool) $flagOrCallback() : (bool) $flagOrCallback;
    }




    public function extract($value)
    {
        if (!$value instanceOf AbstractLeafs) {
            throw new \InvalidArgumentException('$value must be an instance of ' . AbstractLeafs::class);
        }
        /* @var AbstractLeafs $value
         * @var NodeInterface $item */

        $this->setAttachedLeafs($value);

        if (!$this->allowSelectMultipleItems()) {
            $item = $value->getItems()->first();
            return $item ? $item->getValue() : null;
        }

        $data = [];
        foreach ($value->getItems() as $item) {
            $data[] = $item->getValue();
        }

        return $data;
    }

    public function hydrate($value)
    {
        $root = $this->getTreeRoot();
        $object = $this->getAttachedLeafs();
        $items = new ArrayCollection();

        if (!$this->allowSelectMultipleItems()) {
            $value = [$value];
        }

        foreach ($value as $itemValue) {
            $leaf = $this->findLeaf($root, $itemValue);

            if ($leaf) {
                $items->add($leaf);
            }
        }

        $object->setItems($items);

        return $object;
    }

    private function findLeaf(NodeInterface $leaf, $value)
    {
        if ($leaf->getValue() == $value) {
            return $leaf;
        }

        if ($leaf->hasChildren()) {
            foreach ($leaf->getChildren() as $item) {
                $tmpLeaf = $this->findLeaf($item, $value);

                if ($tmpLeaf) { return $tmpLeaf; }
            }
        }

        return null;
    }
}
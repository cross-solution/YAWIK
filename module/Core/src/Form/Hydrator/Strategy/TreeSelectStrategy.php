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
use Core\Entity\Tree\EmbeddedLeafs;
use Core\Entity\Tree\NodeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Hydrator strategy for TreeSelect form element.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class TreeSelectStrategy implements StrategyInterface
{

    /**
     * The selected leafs.
     *
     * @var AbstractLeafs
     */
    private $attachedLeafs;

    /**
     * The root node.
     *
     * @var NodeInterface
     */
    private $treeRoot;

    /**
     * Flag wether multiple selections are allowed.
     *
     * @var bool|callable
     */
    private $allowSelectMultipleItems = false;

    private $shouldCreateLeafs = false;

    private $shouldUseNames = false;

    /**
     * Set the selected leafs.
     *
     * @param AbstractLeafs $attachedLeafs
     *
     * @return self
     */
    public function setAttachedLeafs(AbstractLeafs $attachedLeafs)
    {
        $this->attachedLeafs = $attachedLeafs;

        return $this;
    }

    /**
     * Get the selected leafs.
     *
     * @return AbstractLeafs
     */
    public function getAttachedLeafs()
    {
        return $this->attachedLeafs ?: new EmbeddedLeafs();
    }

    /**
     * Set the root node.
     *
     * @param NodeInterface $treeRoot
     *
     * @return self
     */
    public function setTreeRoot(NodeInterface $treeRoot)
    {
        $this->treeRoot = $treeRoot;

        return $this;
    }

    /**
     * Get the root node.
     *
     * @return NodeInterface
     */
    public function getTreeRoot()
    {
        return $this->treeRoot;
    }

    /**
     * Set the allow multiple selections flag.
     *
     * @param Callable|bool $flagOrCallback When a Callable is passed, it must return bool.
     *
     * @return self
     */
    public function setAllowSelectMultipleItems($flagOrCallback)
    {
        $this->allowSelectMultipleItems = $flagOrCallback;

        return $this;
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    public function allowSelectMultipleItems()
    {
        $flagOrCallback = $this->allowSelectMultipleItems;

        return is_callable($flagOrCallback) ? (bool) $flagOrCallback() : (bool) $flagOrCallback;
    }

    public function setShouldCreateLeafs($flagOrCallback)
    {
        $this->shouldCreateLeafs = $flagOrCallback;

        return $this;
    }

    public function shouldCreateLeafs()
    {
        $flagOrCallback = $this->shouldCreateLeafs;

        return is_callable($flagOrCallback) ? (bool) $flagOrCallback() : (bool) $flagOrCallback;
    }

    /**
     * @return bool
     */
    public function shouldUseNames()
    {
        return $this->shouldUseNames;
    }

    /**
     * @param bool $flag
     *
     * @return self
     */
    public function setShouldUseNames($flag)
    {
        $this->shouldUseNames = (bool) $flag;

        return $this;
    }


    public function extract($value)
    {
        if (empty($value)) {
            return $this->allowSelectMultipleItems() ? [] : null;
        }

        if (!$value instanceof AbstractLeafs) {
            return $value;
        }

        /* @var AbstractLeafs $value
         * @var NodeInterface $item */

        $this->setAttachedLeafs($value);

        if (!$this->allowSelectMultipleItems()) {
            $item = $value->getItems()->first();
            return $item ? $item->getValueWithParents(false, $this->shouldUseNames()) : null;
        }

        $data = [];
        foreach ($value->getItems() as $item) {
            $data[] = $item->getValueWithParents(false, $this->shouldUseNames());
        }

        return $data;
    }

    public function hydrate($value)
    {
        $object = $this->getAttachedLeafs();

        $root = $this->getTreeRoot();
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

    /**
     * Find a leaf with a concrete value in the tree.
     *
     * @param NodeInterface $leaf
     * @param string        $value
     *
     * @return NodeInterface|null
     */
    private function findLeaf(NodeInterface $leaf, $value)
    {
        $parts = is_array($value) ? $value : explode($this->shouldUseNames() ? ' | ': '-', $value);
        $value = array_shift($parts);

        /* @var NodeInterface $item */
        foreach ($leaf->getChildren() as $item) {
            $compare = $this->shouldUseNames() ? $item->getName() : $item->getValue();
            if ($compare == $value) {
                if (count($parts)) {
                    return $this->findLeaf($item, $parts);
                }

                return $item;
            }
        }

        if ($value && $this->shouldCreateLeafs()) {
            $nodeClass = get_class($leaf);
            $node = new $nodeClass($value);
            $leaf->addChild($node);
            if (count($parts)) {
                return $this->findLeaf($node, $parts);
            }

            return $node;
        }

        return null;
    }
}

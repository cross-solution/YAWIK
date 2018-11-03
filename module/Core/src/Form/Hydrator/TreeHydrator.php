<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form\Hydrator;

use Core\Entity\Tree\NodeInterface;
use Doctrine\Common\Collections\Collection;
use Zend\Hydrator\HydratorInterface;

/**
 * This hydrator handles trees for usage in forms.
 *
 * Flatten the tree structure when extracting and
 * rebuilds the tree when hydrating.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class TreeHydrator implements HydratorInterface
{

    /**
     * Hydratable data.
     *
     * @internal
     *      Used as interim storage.
     *
     * @var array
     */
    protected $hydrateData = [];

    /**
     * Extract tree items.
     *
     * Flattens the tree structure to an one dimensional array and returns it in an array
     * under the key'items'.
     *
     * The returned array can be directly bound to \Core\Form\Tree\ManagementFieldset.
     *
     * @param  object $object The root of the tree.
     *
     * @return array
     */
    public function extract($object)
    {
        $data = [];
        $this->flattenTree($object, $data);

        return ['items' => $data ];
    }

    /**
     * Recursively flattens a tree structure.
     *
     * @param NodeInterface $tree
     * @param array $data
     * @param string $curId
     */
    private function flattenTree($tree, &$data, $curId = '1')
    {
        $data[] =
        new \ArrayObject([
            'id' => $tree->getId(),
            'current' => $curId,
            'name' => $tree->getName(),
            'value' => $tree->getValue(),
            'priority' => $tree->getPriority(),
            'do' => 'nothing',
        ]);

        if ($tree->hasChildren()) {
            foreach ($tree->getChildren() as $i => $child) {
                $this->flattenTree($child, $data, $curId . '-' . ($i + 1));
            }
        }
    }

    /**
     * Hydrate a tree structure from form values.
     *
     * Takes the post values for a \Core\Tree\ManegementFieldset and rebuilds the
     * tree structure.
     *
     * @param  array  $data Form values
     * @param  Collection $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->prepareHydrateData($data);

        return $this->hydrateTree($object);
    }

    /**
     * Prepares the form values for hydrating.
     *
     * @internal
     *      Populates the {@link hydrateData} array
     *
     * @param array $data
     */
    private function prepareHydrateData(array $data)
    {
        /*
         * unflatten tree
         */
        $items = $data['items'];
        $tree = [ '__root__' => array_shift($items) ];

        foreach ($items as $item) {
            $parent = substr($item['current'], 0, strrpos($item['current'], '-'));
            $tree[$parent][] = $item;
        }

        $this->hydrateData = $tree;
    }

    /**
     * Recursively hydrate the tree structure.
     *
     * Items not present in the {@link hydrateData} are removed,
     * new items are created accordingly.
     *
     * @param NodeInterface $object
     * @param \ArrayObject $currentData
     *
     * @return NodeInterface
     */
    private function hydrateTree(NodeInterface $object, \ArrayObject $currentData = null)
    {
        if (null === $currentData) {
            $currentData = $this->hydrateData['__root__'];
        }

        if ('set' == $currentData['do']) {
            $object
                ->setName($currentData['name'])
                ->setValue($currentData['value'])
                ->setPriority($currentData['priority'])
            ;
        }

        if (isset($this->hydrateData[$currentData['current']])) {
            foreach ($this->hydrateData[$currentData['current']] as $childData) {
                $child = $this->findOrCreateChild($object, $childData['id']);
                if ('remove' == $childData['do']) {
                    $object->removeChild($child);
                } else {
                    $this->hydrateTree($child, $childData);
                }
            }
        }

        return $object;
    }

    /**
     * Finds an item in a tree structure or create a new item.
     *
     * @param NodeInterface $tree
     * @param $id
     *
     * @return NodeInterface
     */
    private function findOrCreateChild($tree, $id)
    {
        /* @var NodeInterface $node */
        foreach ($tree->getChildren() as $node) {
            if ($id && $node->getId() == $id) {
                return $node;
            }
        }

        $nodeClass = get_class($tree);
        $node = new $nodeClass();
        $tree->addChild($node);

        return $node;
    }
}

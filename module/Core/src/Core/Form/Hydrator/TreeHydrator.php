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

use Core\Entity\Tree\TreeInterface;
use Doctrine\Common\Collections\Collection;
use Zend\Hydrator\HydratorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class TreeHydrator implements HydratorInterface
{

    protected $hydrateData = [];

    /**
     * Extract values from an object
     *
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        $data = [];
        $this->flattenTree($object, $data);

        return ['items' => $data ];

    }

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
                $this->flattenTree($child, $data, $curId . '-' . ($i + 1) );
            }
        }
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  Collection $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $this->prepareHydrateData($data);

        return $this->hydrateTree($object);
    }

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

    private function hydrateTree($object, \ArrayObject $currentData = null)
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

    private function findOrCreateChild($tree, $id)
    {
        /* @var TreeInterface $tree
         * @var TreeInterface $node */
        foreach ($tree->getChildren() as $node) {
            if ($node->getId() == $id) { return $node; }
        }

        $nodeClass = get_class($tree);
        $node = new $nodeClass();
        $tree->addChild($node);

        return $node;
    }
}
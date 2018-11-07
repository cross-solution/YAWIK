<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Service\EntityEraser;

use Core\Entity\EntityInterface;

/**
 * DependencyResult collection
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class DependencyResultCollection implements \IteratorAggregate
{

    /**
     * The DependencyResult objects.
     *
     * @var array
     */
    private $results = [];

    /**
     * Add a result in various formats.
     *
     * Possible are
     * - a DependencyResult object
     * - any \Traversable object (in that case, the name is set to the class name of the first item)
     * - an array of DependencyResult objects.
     * - an array in the format
     *   [ name, entities, options]
     * -an array in the format
     *  [
     *      'name' => name,
     *      'entities' => entities,
     *      'options' => [ ... ]
     *  ]
     * - an array of arrays in any of the two formats above or \Traversable objects. Can be mixed.
     *
     * @param DependencyResult|array|\Traversable|string           $name
     * @param null|array|\Traversable       $entities
     * @param array|null $options
     *
     * @return DependencyResultCollection
     */
    public function add($name, $entities = null, array $options = null)
    {
        if ($name instanceof DependencyResult) {
            return $this->addResult($name);
        }

        if ($name instanceof \Traversable) {
            return $this->addTraversable($name);
        }

        if (is_array($name)) {
            return $this->addArray($name);
        }

        if (null === $entities) {
            throw new \UnexpectedValueException('$entities must not be null.');
        }

        return $this->addArray([
            'name' => $name,
            'entities' => $entities,
            'options' => $options,
        ]);
    }

    /**
     * Add a DependencyResult object.
     *
     * @param DependencyResult $result
     *
     * @return self
     */
    private function addResult(DependencyResult $result)
    {
        $this->results[] = $result;

        return $this;
    }

    /**
     * Add a \Traversable object.
     *
     * Sets the name of the result to the class name of the first item in the object collection.
     * Therefor, the \Traversable object must not be empty.
     *
     * @param \Traversable $result
     *
     * @return DependencyResultCollection
     * @throws \InvalidArgumentException
     */
    private function addTraversable(\Traversable $result)
    {
        /*
         * Since we only know, that $result is an instance of \Traversable
         * (and thus, usable in a foreach), we cannot relay on methods like first()
         * which are defined in the \Iterable interface.
         * We must use a foreach to get the first item.
         *
         * Because PHP does not unset the loop variable, this works.
         */
        foreach ($result as $item) {
            break;
        }

        /* @var null|object|mixed $item */
        if (!$item instanceof EntityInterface) {
            throw new \InvalidArgumentException('Traversable objects must be a non-empty collection of Entity instances.');
        }

        $name = get_class($item);

        return $this->addArray([
            'name' => $name,
            'entities' => $result,
        ]);
    }

    /**
     * Add an array.
     *
     * please see {@link add} for a list of possible values.
     *
     * @param array $result
     *
     * @return self
     */
    private function addArray(array $result)
    {
        if (1 < count($result) && !isset($result['name']) && !is_string($result[0])) {
            foreach ($result as $r) {
                if (is_array($r)) {
                    $this->add($r);
                } else {
                    return $this->addTraversable(new \ArrayIterator($result));
                }
            }
            return $this;
        }

        if (is_string($result[0])) {
            $result = [
                'name' => $result[0],
                'entities' => isset($result[1]) ? $result[1] : null,
                'options' => isset($result[2]) && is_array($result[2])
                    ? $result[2]
                    : [
                        'description' => isset($result[2]) ? $result[2] : null,
                        'viewScript' => isset($result[3]) ? $result[3] : null,
                      ],
            ];
        }

        if (!isset($result['name']) || !isset($result['entities'])) {
            throw new \UnexpectedValueException('Array must have the keys "name" and "entities".');
        }

        if (!count($result['entities'])) {
            throw new \UnexpectedValueException('Entities must be non-empty.');
        }

        $result = new DependencyResult($result['name'], $result['entities'], isset($result['options']) ? $result['options'] : null);

        return $this->addResult($result);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }
}

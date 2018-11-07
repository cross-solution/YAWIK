<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Collection;

use Doctrine\Common\Collections\Collection;
use Closure;

class IdentityWrapper implements Collection
{
    
    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var callable
     */
    protected $identityExtractor;
    
    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }
    
    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->collection->count();
    }
    
    /**
     * {@inheritDoc}
     */
    public function add($element)
    {
        return $this->collection->add($element);
    }
    
    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->collection->clear();
    }
    
    /**
     * {@inheritDoc}
     */
    public function contains($element)
    {
        return $this->collection->contains($element);
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }
    
    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $element = $this->getElement($key);
        
        if ($element !== false && $this->collection->removeElement($element)) {
            return $element;
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function removeElement($element)
    {
        return $this->collection->removeElement($element);
    }
    
    /**
     * {@inheritDoc}
     */
    public function containsKey($key)
    {
        return $this->getElement($key) !== false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        $element = $this->getElement($key);
        
        return $element !== false ? $element : null;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return $this->collection->map(function ($element) {
            return $this->getKey($element);
        })->toArray();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return $this->collection->getValues();
    }
    
    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        if ($this->getElement($key) !== false) {
            $this->collection->set($this->collection->indexOf($value), $value);
        } else {
            $this->collection->add($value);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $array = [];
        
        foreach ($this->collection as $element) {
            $array[$this->getKey($element)] = $element;
        }
        
        return $array;
    }
    
    /**
     * {@inheritDoc}
     */
    public function first()
    {
        return $this->collection->first();
    }
    
    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return $this->collection->last();
    }
    
    /**
     * {@inheritDoc}
     */
    public function key()
    {
        $element = $this->collection->current();
        
        if ($element !== false) {
            return $this->getKey($element);
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->collection->current();
    }
    
    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return $this->collection->next();
    }
    
    /**
     * {@inheritDoc}
     */
    public function exists(Closure $p)
    {
        foreach ($this->collection as $element) {
            $key = $this->getKey($element);
            if ($p($key, $element)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static($this->collection->filter($p));
    }
    
    /**
     * {@inheritDoc}
     */
    public function forAll(Closure $p)
    {
        foreach ($this->collection as $element) {
            $key = $this->getKey($element);
            if (!$p($key, $element)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        $mapped = $this->collection->map($func);
        
        // validate mapped elements
        array_map($this->getIdentityExtractor(), $mapped->toArray());
        
        return new static($mapped);
    }
    
    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $array = $this->toArray();
        $this->collection->clear();
        
        foreach ($array as $key => $element) {
            $this->collection->set($key, $element);
        }
        
        $partitions = $this->collection->partition($p);
        
        return [new static($partitions[0]), new static($partitions[1])];
    }
    
    /**
     * {@inheritDoc}
     */
    public function indexOf($element)
    {
        return $this->collection->indexOf($element) === false ? false : $this->getKey($element);
    }
    
    /**
     * {@inheritDoc}
     */
    public function slice($offset, $length = null)
    {
        $array = [];
        
        foreach ($this->collection->slice($offset, $length) as $element) {
            $array[$this->getKey($element)] = $element;
        }
        
        return $array;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }
    
    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return (bool)$this->getElement($offset);
    }
    
    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            return $this->add($value);
        }
        
        $this->set($offset, $value);
    }
    
    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        return $this->remove($offset);
    }
    
    /**
     * @param callable $identityExtractor
     * @return IdentityWrapper
     */
    public function setIdentityExtractor(callable $identityExtractor)
    {
        $this->identityExtractor = $identityExtractor;
        
        return $this;
    }

    /**
     *
     * @return \Core\Collection\callable
     */
    protected function getIdentityExtractor()
    {
        if (!isset($this->identityExtractor)) {
            // default identity extractor
            $this->identityExtractor = function ($element) {
                if (!is_callable([$element, 'getId'])) {
                    throw new \LogicException('$element must have getId() method');
                }
                
                return $element->getId();
            };
        }
        
        return $this->identityExtractor;
    }

    /**
     * @param mixed $element
     * @return mixed
     */
    protected function getKey($element)
    {
        return call_user_func($this->getIdentityExtractor(), $element);
    }

    /**
     * @param mixed $element
     * @return mixed
     */
    protected function getElement($key)
    {
        return $this->collection->filter(function ($element) use ($key) {
            return $this->getKey($element) == $key;
        })->first();
    }
}

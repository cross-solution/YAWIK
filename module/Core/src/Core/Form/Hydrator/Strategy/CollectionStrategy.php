<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** CollectionStrategy.php */
namespace Core\Form\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use Doctrine\Common\Collections\Collection;

class CollectionStrategy implements StrategyInterface
{
    protected $prototype;
    
    public function __construct(Collection $collectionPrototype = null)
    {
        if (null !== $collectionPrototype) {
            $this->setCollectionPrototype($collectionPrototype);
        }
    }
    
    public function setCollectionPrototype(Collection $collection)
    {
        $this->prototype = $collection;
        return $this;
    }
    
    public function getCollection()
    {
        if (!$this->prototype) {
            $this->setCollectionPrototype(new \Core\Entity\Collection\ArrayCollection());
        }
        return clone $this->prototype;
    }
    
    public function extract($value)
    {
        if (!$value instanceof Collection) {
            throw new \InvalidArgumentException('Value must implement \Doctrine\Common\Collections\Collection');
        }
        
        return $value->toArray();
    }

    public function hydrate($value)
    {
        if ($value instanceof Collection) {
            return $value;
        }
        
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array');
        }
        
        return $this->getCollection()->fromArray($value);
        
    }
}

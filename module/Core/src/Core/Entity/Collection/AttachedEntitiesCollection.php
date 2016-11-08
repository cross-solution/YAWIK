<?php
/**
 * YAWIK
*
* @filesource
* @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
* @license   MIT
* @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
* @since 0.28
*/

namespace Core\Entity\Collection;

use Core\Repository\RepositoryService;
use Core\Entity\AttachedEntityReference;
use Core\Entity\IdentifiableEntityInterface;
use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Closure;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\Criteria;

class AttachedEntitiesCollection extends ArrayCollection
{

    /**
     * @var RepositoryService
     */
    protected $repositories;

    /**
     * @var AttachedEntityReference
     */
    protected $referencePrototype;

    /**
     * @param RepositoryService $repositories
     * @param AttachedEntityReference $referencePrototype
     * @param array $elements
     */
    public function __construct(RepositoryService $repositories, AttachedEntityReference $referencePrototype = null, array $elements = [])
    {
        parent::__construct($elements);
        $this->repositories = $repositories;
        $this->referencePrototype = $referencePrototype ?: new AttachedEntityReference();
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Collections\ArrayCollection::set()
     */
    public function setAttachedEntity($key, $entity)
    {
        if (! $entity instanceof IdentifiableEntityInterface) {
            throw new InvalidArgumentException(sprintf('$entity must be instance of %s', IdentifiableEntityInterface::class));
        }
        
        $className = get_class($entity);
        
        if (null === $key) {
            $key = $className;
        }
        
        $reference = clone $this->referencePrototype;
        $entityId = $entity->getId();
        
        // check if entity is not persisted
        if (! $entityId) {
            // persist entity & retrieve its ID
            $this->repositories->getRepository($className)
                ->store($entity);
            $entityId = $entity->getId();
        }
        
        $reference->setRepository($className)
            ->setEntityId($entityId);
        
        return parent::set($key, $reference);
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Collections\ArrayCollection::get()
     */
    public function getAttachedEntity($key)
    {
        $reference = parent::get($key);
        
        if (! $reference) {
            return;
        }
        
        $entity = $this->doGetEntity($reference);
        
        if (! $entity) {
            // remove reference if entity does not exists
            unset($this->elements[$key]);
        }
        
        return $entity;
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Collections\ArrayCollection::remove()
     */
    public function removeAttachedEntity($key)
    {
        $reference = parent::remove($key);
        
        if (! $reference) {
            return;
        }
        
        $entity = $this->doGetEntity($reference);
        
        if ($entity) {
            $this->repositories->getRepository($reference->getRepository())
                ->remove($entity);
        }
        
        return $reference;
    }

    /**
     * @param AttachedEntityReference $reference
     * @return object|NULL
     */
    protected function doGetEntity(AttachedEntityReference $reference)
    {
        return $this->repositories->getRepository($reference->getRepository())
            ->find($reference->getEntityId());
    }
    
    /**
     * {@inheritDoc}
     */
    public function map(Closure $func)
    {
        return new static($this->repositories, $this->referencePrototype, array_map($func, $this->toArray()));
    }
    
    /**
     * {@inheritDoc}
     */
    public function filter(Closure $p)
    {
        return new static($this->repositories, $this->referencePrototype, array_filter($this->toArray(), $p));
    }
    
    /**
     * {@inheritDoc}
     */
    public function partition(Closure $p)
    {
        $matches = $noMatches = array();
    
        foreach ($this->toArray() as $key => $element) {
            if ($p($key, $element)) {
                $matches[$key] = $element;
            } else {
                $noMatches[$key] = $element;
            }
        }
    
        return array(new static($this->repositories, $this->referencePrototype, $matches),
            new static($this->repositories, $this->referencePrototype, $noMatches));
    }
    
    /**
     * {@inheritDoc}
     */
    public function matching(Criteria $criteria)
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = $this->toArray();
    
        if ($expr) {
            $visitor  = new ClosureExpressionVisitor();
            $filter   = $visitor->dispatch($expr);
            $filtered = array_filter($filtered, $filter);
        }
    
        if ($orderings = $criteria->getOrderings()) {
            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering == Criteria::DESC ? -1 : 1);
            }
    
            uasort($filtered, $next);
        }
    
        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();
    
        if ($offset || $length) {
            $filtered = array_slice($filtered, (int)$offset, $length);
        }
    
        return new static($this->repositories, $this->referencePrototype, $filtered);
    }
}

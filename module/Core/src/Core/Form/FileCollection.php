<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** FileCollection.php */ 
namespace Core\Form;

use Zend\Form\Element\Collection;
use Core\Form\File;
use Core\Repository\EntityBuilder\EntityBuilderInterface;
use Core\Entity\CollectionInterface;
use Core\Entity\EntityInterface;
use Core\Entity\Collection as EntityCollection;
use Core\Entity\FileEntity;

class FileCollection extends Collection
{
    protected $entityCollectionPrototype;
    protected $entityPrototype;
    
    public function setEntityCollectionPrototype(CollectionInterface $collection)
    {
        $this->entityCollectionPrototype = $collection;
        return $this;
    }
    
    public function getEntityCollection()
    {
        if (!$this->entityCollectionPrototype) {
            $this->setEntityCollectionPrototype(new EntityCollection());
        }
        return clone $this->entityCollectionPrototype;
    }
    
    public function setEntityPrototype(EntityInterface $entity)
    {
        $this->entityPrototype = $entity;
        return $this;
    }
    
    public function getEntity()
    {
        if (!$this->entityPrototype) {
            $this->setEntityPrototype(new FileEntity());
        }
        return clone $this->entityPrototype;
    }
    
    public function bindValues(array $values = array())
    {
        $hydrator = $this->getHydrator();
        $collection = $this->getEntityCollection();
        
        foreach ($values as $name => $value) {
            $element = $this->get($name);
    
            $entity = $hydrator->hydrate($value, $this->getEntity());
            $collection->add($entity);
        }
    
        return $collection;
    }
}


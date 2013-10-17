<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** HistoryCollection.php */ 
namespace Applications\Entity;

use Core\Entity\Collection;
use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;

class HistoryCollection extends Collection implements HistoryCollectionInterface
{
 
    protected $builder;
    
    public function setEntityBuilder(EntityBuilderInterface $builder)
    {
        $this->builder = $builder;
        return $this;
    }
    
    public function getEntityBuilder()
    {
        return $this->builder;
    }
    
    public function addFromStatus($status, $message='[System]') {
        if (!$status instanceOf StatusInterface) {
            $status = new Status($status);
        }
            
        $history = $this->getEntityBuilder()->build(array(
            'status'  => $status,
            'date'    => new \DateTime(),
            'message' => $message
        ));
        
        return $this->add($history);
    }
    
    public function add(EntityInterface $entity)
    {
        array_unshift($this->collection, $entity);
        return $this;
    }

}


<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * peronal informations of a subscriber
 * 
 * @ODM\Document (collection="applications.subscribers", repositoryClass="Applications\Repository\Subscriber")
 */
class Subscriber extends AbstractIdentifiableEntity 
{  
    /** @ODM\String */
    protected $name;
    
    /** @ODM\String */
    protected $uri;
   
    protected $repository;
    
    public function injectRepository($repository) {
        $this->repository = $repository;
        return $this;
    }
    
    protected function getRepository($repository) {
        return $this->repository;
    }
    
    public function getName()
    {
        if (empty($this->name)) {
            // TODO: jede Menge
            $this->name = '';
            $this->getRepository();
            
        }
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
   
    public function getUri()
    {
        return $this->uri;
    }
    
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
    
}
    
    
   

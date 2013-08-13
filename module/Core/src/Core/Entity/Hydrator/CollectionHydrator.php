<?php

namespace Core\Entity\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Entity\CollectionInterface;

class CollectionHydrator extends AbstractHydrator
{
    protected $entityHydrator;
	
	public function __construct($entityHydrator, $entityPrototype)
	{
	    parent::__construct();
	    $this->entityHydrator = $entityHydrator;
	    $this->entityPrototype = $entityPrototype;
	    $this->init();
	}
	
	protected function init()
	{ } 
	
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        if (!$object instanceOf CollectionInterface) {
            return array();
            //@todo Error-Handling
        }
        
        $result = array();
        foreach ($object as $entity) {
            $result[] = $this->entityHydrator->extract($entity);
        }
        return $result;
                
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::hydrate()
     */
    public function hydrate (array $data, $object)
    {
        if (!$object instanceOf CollectionInterface) {
            return array();
            //@todo Error-Handling
        }

        foreach ($data as $entityData) {
            $entity = clone $this->entityPrototype;
            $object->add($this->entityHydrator->hydrate($entityData, $entity));
        }
        return $object;
    }
    
   
    
}
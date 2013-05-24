<?php

namespace Core\Model\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Model\CollectionInterface;

class CollectionHydrator extends AbstractHydrator
{
    protected $modelHydrator;
	
	public function __construct($modelHydrator, $modelPrototype)
	{
	    parent::__construct();
	    $this->modelHydrator = $modelHydrator;
	    $this->modelPrototype = $modelPrototype;
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
        foreach ($object as $model) {
            $result[] = $this->modelHydrator->extract($model);
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

        foreach ($data as $modelData) {
            $model = clone $this->modelPrototype;
            $object->addModel($this->modelHydrator->hydrate($modelData, $model));
        }
        return $object;
    }
    
    public function hydrateValue($name, $value) {
        if ($this->hasStrategy($name)) {
            return parent::hydrateValue($name, $value);
        }
        
        return $value;
    }
    
    public function extractValue($name, $value)
    {
        if ($this->hasStrategy($name)) {
            return parent::extractValue($name, $value);
        }
        
        return $value;
    }
    
}
<?php

namespace Core\Entity\Hydrator;


use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Entity\EntityInterface;

class EntityHydrator extends AbstractHydrator
{
	
	public function __construct()
	{
	    parent::__construct();
	    $this->init();
	}
	
	protected function init()
	{ } 
	
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        if (!$object instanceOf EntityInterface) {
            return array();
            //@todo Error-Handling
        }
        
        $getters = array_filter(
            get_class_methods($object),
            function ($methodName) use ($object) {
                return "get" === substr($methodName, 0, 3)
                       && method_exists($object, 's' . substr($methodName, 1));
            }
        );

        $data = array();
        foreach ($getters as $getter) {
            $propertyValue = $object->$getter();
            $propertyName = lcfirst(substr($getter, 3));
            
            $data[$propertyName] = $this->extractValue($propertyName, $propertyValue);
        }
        return $data;
        
    }

	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::hydrate()
     */
    public function hydrate (array $data, $object)
    {
        if (!$object instanceOf EntityInterface) {
            return array();
            //@todo Error-Handling
        }
        $setters = array_filter(
            get_class_methods($object),
            function ($methodName) {
                return "set" === substr($methodName, 0, 3);
            }
        );
        
        foreach ($setters as $setter) {
            $propertyName = lcfirst(substr($setter, 3));
            if (isset($data[$propertyName])) {
                $object->$setter($this->hydrateValue($propertyName, $data[$propertyName]));
                unset($data[$propertyName]);
            }
        }
        
        foreach ($data as $key => $value) {
            if ($value instanceOf \MongoId) {
                $object->setId($value->__toString());
            }
        }
        
        return $object;
    }
    
}
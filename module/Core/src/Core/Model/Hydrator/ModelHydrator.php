<?php

namespace Core\Model\Hydrator;

use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Model\ModelInterface;

class ModelHydrator extends AbstractHydrator
{
	
	/* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */
    public function extract ($object)
    {
        if (!$object instanceOf ModelInterface) {
            return array();
            //@todo Error-Handling
        }
        
        $getters = array_filter(
            get_class_methods($object),
            function ($methodName) {
                return "get" === substr($methodName, 0, 3);
            }
        );

        $data = array();
        foreach ($getters as $getter) {
            if (!method_exists($object, 's' . substr($getter, 1))) {
                continue;
            }
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
        if (!$object instanceOf ModelInterface) {
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
            }
        }
        
        return $object;
    }
    
    public function hydrateValue($name, $value) {
        if ($this->hasStrategy($name)) {
            return parent::hydrateValue($name, $value);
        }
        if (is_array($value)) {
            $collection = new \Core\Model\Collection($value);
            return $collection;
        }
        return $value;
    }
    
}
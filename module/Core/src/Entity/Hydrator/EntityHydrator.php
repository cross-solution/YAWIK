<?php

namespace Core\Entity\Hydrator;

use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\MethodMatchFilter;
use Core\Entity\EntityInterface;
use MongoDB\BSON\ObjectId;

class EntityHydrator extends AbstractHydrator
{
    public function __construct()
    {
        $this->init();
    }
    
    protected function init()
    {
    }
    
    /* (non-PHPdoc)
     * @see \Laminas\Hydrator\HydratorInterface::extract()
     */
    public function extract($object): array
    {
        if (!$object instanceof EntityInterface) {
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
            if ($this->getFilter()->filter($propertyName)) {
                $data[$propertyName] = $this->extractValue($propertyName, $propertyValue, $object);
            }
        }
        return $data;
    }

    /* (non-PHPdoc)
     * @see \Laminas\Hydrator\HydratorInterface::hydrate()
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof EntityInterface) {
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
                $value = $this->hydrateValue($propertyName, $data[$propertyName]);
                if (null !== $value) {
                    $object->$setter($value);
                }
                unset($data[$propertyName]);
            }
        }
        
        foreach ($data as $key => $value) {
            if ($value instanceof ObjectId) {
                $object->setId($value->__toString());
            }
        }
        
        return $object;
    }

    /**
     * exclude methods from the automatism
     *
     * @param $methods
     */
    public function setExcludeMethods($methods)
    {
        if (is_string($methods)) {
            $methods = array($methods);
        }
        foreach ($methods as $method) {
            $this->addFilter($method, new MethodMatchFilter($method), FilterComposite::CONDITION_AND);
        }
    }
}

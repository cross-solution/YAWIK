<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** SettingsEntityHydrator.php */ 
namespace Settings\Entity\Hydrator;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Settings\Entity\SettingsContainerInterface;
use Zend\Stdlib\Hydrator\AbstractHydrator;

class SettingsEntityHydrator extends AbstractHydrator
{
    public function extract ($object)
    {
        if (!$object instanceOf SettingsContainerInterface) {
            return array();
            //@todo Error-Handling
        }
    
        $getters = array_filter(
            get_class_methods($object),
            function ($methodName) use ($object) {
                return "get" === substr($methodName, 0, 3)
                && method_exists($object, 's' . substr($methodName, 1))
                && "getSettings" != $methodName
                && "get" !== $methodName;
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
                return "set" === substr($methodName, 0, 3)
                       && "setSettings" !== $methodName
                       && "set" !== $methodName;
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
            if ($value instanceOf \MongoId) {
                $object->setId($value->__toString());
            }
        }
    
        return $object;
    }
    
    
}


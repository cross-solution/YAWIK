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
use Zend\Stdlib\Hydrator\Reflection;

class SettingsEntityHydrator extends Reflection
{
    
    public function __construct()
    {
        parent::__construct();
        $this->addFilter('ignoreInternalProperties', function($property) { return "_" != $property{0}; });
    }
    
/**
     * Extract values from an object
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $result = array();
        foreach (self::getReflProperties($object) as $property) {
            $propertyName = $property->getName();
            if (!$this->filterComposite->filter($propertyName)) {
                continue;
            }
            $getter = 'get' . ucfirst($propertyName);
            $value = method_exists($object, $getter)
                   ? $object->$getter()
                   : $property->getValue($object);

            $result[$propertyName] = $this->extractValue($propertyName, $value);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $reflProperties = self::getReflProperties($object);
        foreach ($data as $key => $value) {
            if (isset($reflProperties[$key])) {
                $value  = $this->hydrateValue($key, $value);
                $setter = 'set' . ucfirst($key);
                if (method_exists($object, $setter)) { 
                    $object->$setter($value);
                } else {
                    // the values of the entity have to be set explicitly
                    $reflProperties[$key]->setValue($object, $this->hydrateValue($key, $value));
                }
            }
        }
        return $object;
    }
    
}


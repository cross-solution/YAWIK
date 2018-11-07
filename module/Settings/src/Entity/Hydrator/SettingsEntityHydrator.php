<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SettingsEntityHydrator.php */
namespace Settings\Entity\Hydrator;

use Zend\Hydrator\Reflection;

class SettingsEntityHydrator extends Reflection
{

    /**
     * Creates a SettingsEntityHydrator
     */
    public function __construct()
    {
        parent::__construct();
        $this->addFilter(
            'ignoreInternalProperties',
            function ($property) {
                return "_" != $property{0};
            }
        );
    }

    /**
     * {@inheritDoc}
     *
     * Uses a getter method for each property if one exists.
     */
    public function extract($object)
    {
        $result = array();
        foreach (self::getReflProperties($object) as $property) {
            /* @var $property \ReflectionProperty */
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
     * {@inheritDoc}
     *
     * Uses the setter method if one exists.
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
                    /* @var $property \ReflectionProperty */
                    $property = $reflProperties[$key];
                    $property->setValue($object, $value);
                }
            }
        }
        return $object;
    }
}

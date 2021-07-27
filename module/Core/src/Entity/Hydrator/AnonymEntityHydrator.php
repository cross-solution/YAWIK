<?php

namespace Core\Entity\Hydrator;

#use Core\Entity\AnonymEntityInterface;
#use Settings\Entity\AwareEntity;

use Laminas\Hydrator\AbstractHydrator;
use Settings\Entity\InitializeAwareSettingsContainerInterface;

class AnonymEntityHydrator extends AbstractHydrator
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
        $data = array();
        if (is_array($object) || $object instanceof \Traversable) {
            foreach ($object as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /* (non-PHPdoc)
     * @see \Laminas\Hydrator\HydratorInterface::hydrate()
     */
    public function hydrate(array $data, $object)
    {
        // @TODO: undefined methods
        /*
        if ($object instanceof AwareSettings) {
            $setterMethods = $object->getSetters();
        }
        */
        $setterMethods = array();
        if(method_exists($object, 'getSetters')){
            $setterMethods = $object->getSetters();
        }

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (in_array($setter, $setterMethods)) {
                $object->$setter($value);
            } else {
                $object->$key = $value;
            }
        }
        return $object;
    }
}

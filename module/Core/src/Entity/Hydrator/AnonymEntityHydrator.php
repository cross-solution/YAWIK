<?php

namespace Core\Entity\Hydrator;

use Zend\Hydrator\AbstractHydrator;
use Core\Entity\AnonymEntityInterface;
use Settings\Entity\AwareEntity;

class AnonymEntityHydrator extends AbstractHydrator
{
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    protected function init()
    {
    }

    /* (non-PHPdoc)
     * @see \Zend\Hydrator\HydratorInterface::extract()
     */
    public function extract($object)
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
     * @see \Zend\Hydrator\HydratorInterface::hydrate()
     */
    public function hydrate(array $data, $object)
    {
        $setterMethods = array();
        if ($object instanceof AwareEntity) {
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

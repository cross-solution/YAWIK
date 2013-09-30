<?php

namespace Core\Entity\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;
use Core\Entity\EntityInterface;

class AnonymEntityHydrator extends AbstractHydrator {

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    protected function init() {
        
    }

    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::extract()
     */

    public function extract($object) {
        $data = array();
        if (is_array( $object) || $object instanceof \Traversable) {
            foreach ($object as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /* (non-PHPdoc)
     * @see \Zend\Stdlib\Hydrator\HydratorInterface::hydrate()
     */

    public function hydrate(array $data, $object) {
        foreach ($data as $key=>$value) {
            $object->$key = $value;
        }
        return $object;
    }

}
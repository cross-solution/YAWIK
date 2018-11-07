<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity\Hydrator;

use Zend\Hydrator\AbstractHydrator;
use Core\Entity\EntityInterface;

class JsonEntityHydrator extends AbstractHydrator
{
    protected $arrayHydrator;
    protected $excludeMethods;

    public function __construct()
    {
        parent::__construct();
        $this->init();
        $this->excludeMethods = array();
    }

    protected function init()
    {
    }

    public function injectHydrator($hydrator)
    {
        if (empty($hydrator) || !$hydrator instanceof AbstractHydrator) {
            throw new \InvalidArgumentException("Hydrator for JsonEntityHydrator must be assured");
        }
        $this->arrayHydrator = $hydrator;
        return $this;
    }

    protected function getHydrator()
    {
        return $this->arrayHydrator;
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        return $object;
    }

    /**
     * @param object $object
     * @return array|string
     * @throws \InvalidArgumentException
     */
    public function extract($object)
    {
        if (!$object instanceof EntityInterface) {
            throw new \InvalidArgumentException("Extract only from Entities");
        }
        $hydrator = $this->getHydrator();
        $hydrator->setExcludeMethods($this->excludeMethods);
        $array = $hydrator->extract($object);
        return json_encode($array);
    }

    public function setExcludeMethods(array $methods)
    {
        $this->excludeMethods = $methods;
        return $this;
    }
}

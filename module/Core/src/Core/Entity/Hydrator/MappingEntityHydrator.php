<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Hydrator;

use Core\Entity\EntityInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class MappingEntityHydrator extends EntityHydrator
{
    protected $map = [];

    public function __construct(array $map = [])
    {
        $this->setPropertyMap($map);

        parent::__construct();
    }

    public function setPropertyMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    public function getPropertyMap()
    {
        return $this->map;
    }

    public function extract($object)
    {

        $data = parent::extract($object);

        foreach ($this->map as $from => $to) {
            if (array_key_exists($from, $data)) {
                $data[$to] = $this->extractValue($to, $data[$from], $object);
                unset($data[$from]);
            }
        }

        return $data;
    }

    public function hydrate(array $data, $object)
    {
        foreach ($this->map as $to => $from) {
            if (array_key_exists($from, $data)) {
                $data[$to] = $this->hydrateValue($from, $data[$from], $data);
                unset($data[$from]);
            }
        }

        return parent::hydrate($data, $object);
    }
}
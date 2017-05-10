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

/**
 * This hydrator allows the mapping of entity properties to data array keys.
 *
 * With the property map
 * <pre>
 * [
 *      'property' => 'arraykey'
 * ];
 * </pre>
 *
 * The value from the "getProperty" method is set in the data array key "arraykey".
 *
 * Strategies can be added for BOTH sides of the map.
 * When extracting, the strategy attached to the right side is called AFTER the extraction from the object.
 * When hydrating, the strategy for the right side is called BEFORE hydrating the object.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.28
 * @since 0.29 add documentation and tests.
 */
class MappingEntityHydrator extends EntityHydrator
{
    /**
     * Property map.
     * <pre>
     * [
     *      'property' => 'arraykey',
     * ]
     * </pre>
     *
     * @var array
     */
    protected $map = [];

    /**
     * @param array $map The property map to use.
     */
    public function __construct(array $map = [])
    {
        $this->setPropertyMap($map);

        parent::__construct();
    }

    /**
     * @param array $map
     *
     * @return self
     */
    public function setPropertyMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @return array
     */
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
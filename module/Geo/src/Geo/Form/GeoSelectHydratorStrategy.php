<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Geo\Service\AbstractClient;
use Core\Entity\AbstractLocation as Location;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Strategy to hydrate/extract a location entity.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 * @since 0.29.2 - add support for multiple locations.
 * @todo write test 
 */
class GeoSelectHydratorStrategy implements StrategyInterface
{
    protected $geoClient;

    protected $locationEntityPrototype;

    public function __construct(AbstractClient $client)
    {
        $this->geoClient = $client;
    }

    /**
     * @param mixed $locationEntityPrototype
     *
     * @return self
     */
    public function setLocationEntityPrototype($locationEntityPrototype)
    {
        $this->locationEntityPrototype =
            is_string($locationEntityPrototype)
            ? new $locationEntityPrototype
            : $locationEntityPrototype;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationEntity()
    {
        return clone $this->locationEntityPrototype;
    }



    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param mixed  $value  The original value.
     *
     * @return mixed Returns the value that should be extracted.
     */
    public function extract($value, $object = null)
    {
        if ($value instanceOf Collection || is_array($value)) {
            $values = [];
            foreach ($value as $collItem) {
                $values[] = $this->extract($collItem, $object);
            }
            return $values;
        }

        if ($value instanceOf Location) {
            return $value->toString();
        }

        if (0 === strpos($value, '{')) {
            return $value;
        }
        if ($value){
            $data = $this->geoClient->queryOne($value);
            return $data['id'];
        }else{
            return;
        }
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @param array $data  (optional) The original data for context.
     *
     * @return mixed Returns the value that should be hydrated.
     */
    public function hydrate($value, $data = [])
    {
        if (is_array($value)) {
            $coll = new ArrayCollection();
            foreach ($value as $v) {
                $coll->add($this->hydrate($v, $data));
            }
            return $coll;
        }

        if (empty($value) || 0 !== strpos($value, '{')) {
            return null;
        }

        $location = $this->getLocationEntity();
        return $location->fromString($value);
    }
}
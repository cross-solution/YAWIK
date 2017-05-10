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

use Geo\Service\AbstractClient;
use Jobs\Entity\Location;
use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
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
        if (empty($value) || 0 !== strpos($value, '{')) {
            return null;
        }

        $location = $this->getLocationEntity();
        return $location->fromString($value);
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Form\GeoText;

use Geo\Entity\Geometry\Point;
use Jobs\Entity\Coordinates;
use Jobs\Entity\Location;
use Zend\Json\Json;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Converter 
{

    public function toEntity($data, $type)
    {
        if ('photon' == $type) {
            $data = $this->normalizePhotonData($data);
        }

        if (empty($data)) {
            return new Location();
        }
        $entity = new Location();
        $entity->setCity($data['city'])
               ->setRegion($data['region'])
               ->setPostalcode($data['postalcode'])
               ->setCountry($data['country']);
        if (!empty($data['coordinates'])) {
               $entity->setCoordinates(new Point($data['coordinates']));
        }



        return $entity;

    }

    protected function normalizePhotonData($data)
    {
        if (empty($data)) {
            return [];
        }

        $data = Json::decode($data, Json::TYPE_ARRAY);

        $data = [
            'city' => isset($data['properties']['city']) ? $data['properties']['city'] : null,
            'region' => isset($data['properties']['state']) ? $data['properties']['state'] : null,
            'postalcode' => isset($data['properties']['postcode']) ? $data['properties']['postcode'] : null,
            'country' => isset($data['properties']['country']) ? $data['properties']['country'] : null,
            'coordinates' => isset($data['geometry']['coordinates']) ? $data['geometry']['coordinates'] : null,
        ];

        return $data;
    }

    public function toValue(Location $location, $type)
    {
        if ('photon' == $type) {
            $data = [
                "geometry" => [
                    "coordinates" => $location->getCoordinates()->getCoordinates(),
                    "type" => $location->getCoordinates()->getType()
                ],
                "type" => "Feature",
                "properties" => [
                    "country" => $location->getCountry(),
                    "city" => $location->getCity(),
                    "state" => $location->getRegion(),
                    "postcode" => $location->getPostalcode()
                ]
            ];
        } else {
            $data = [];
        }

        return  $location->getCity() . '|' . Json::encode($data);

    }
}
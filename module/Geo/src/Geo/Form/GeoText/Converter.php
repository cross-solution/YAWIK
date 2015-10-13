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
            'city' => $data['properties']['city'],
            'region' => $data['properties']['state'],
            'postalcode' => isset($data['properties']['postcode']) ? $data['properties']['postcode'] : null,
            'country' => $data['properties']['country'],
            'coordinates' => $data['geometry']['coordinates'],
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
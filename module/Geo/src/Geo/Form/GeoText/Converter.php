<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Form\GeoText;

use Core\Entity\LocationInterface;
use Geo\Entity\Geometry\Point;
use Jobs\Entity\Location;
use Zend\Http\Client;
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

    public function toValue(LocationInterface $location, $type)
    {
        if ('photon' == $type) {
            $coordinates = $location->getCoordinates();
            $data = [
                "geometry" => [
                    "coordinates" => $coordinates?$coordinates->getCoordinates():[0,0],
                    "type" => $coordinates?$coordinates->getType():'Point'
                ],
                "type" => "Feature",
                "properties" => [
                    "country" => $location->getCountry(),
                    "city" => $location->getCity(),
                    "state" => $location->getRegion(),
                    "postcode" => $location->getPostalcode()
                ]
            ];
            return  $location->getCity() . '|' . Json::encode($data);
        } else {
            return $location->getCity() . ', ' . $location->getRegion();
        }
    }

    /**
     * used by the beo plugin only. We can hardcode the geoCoderUrl for the moment
     *
     * @param $input
     *
     * @return array|mixed|string
     */
    public function toCoordinates($input) {
        $client = new Client('http://api.cross-solution.de/geo');
        $client->setMethod('GET');
        $client->setParameterGet(array('q' => $input, 'country' => 'DE', 'coor' => 1, 'zoom' => 1 , 'strict' => 0));
        $response = $client->send();
        $result = $response->getBody();
        $result = json_decode($result);
        $result = (array) $result->result;
        return $result;
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Service;

use Zend\Http\Client;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Photon extends AbstractClient
{


    protected function setupClient($uri)
    {
        $client = new Client();
        $client->setMethod('GET');

        $osmTags = [
            'tourism','aeroway','railway', 'amenity', 'historic', 'tunnel', 'mountain_pass',
            'leisure', 'natural', 'bridge', 'waterway'
        ];

        $osmTags = array_map(function($i) { return urlencode('!' . $i); }, $osmTags);

        $uri = sprintf(
            '%s?osm_tag=%s',
            $uri,
            implode('&osm_tag=', $osmTags)
        );

        $client->setUri($uri);

        return $client;
    }

    protected function preQuery($term, array $params)
    {
        $query = $this->client->getRequest()->getQuery();
        $query->set('q', $term)->set('lon', '10.4486')->set('lat', '51.1641');

        if (isset($params['lang'])) {
            $query->set('lang', $params['lang']);
        }
    }

    protected function processResult($result)
    {
        $result = json_decode($result);
        $result = $result->features;
        $r=[];

        foreach ($result as $key => $val) {
            $row=[
                  'postalCode' => (property_exists($val->properties, 'postcode') ? $val->properties->postcode:''),
                  'city' =>(property_exists($val->properties, 'city') ? $val->properties->city:''),
                  'street' => (property_exists($val->properties, 'street') ? $val->properties->street : ''),
                  'region' => (property_exists($val->properties, 'state') ? $val->properties->state : ''),
                  'country' => (property_exists($val->properties, 'country') ? $val->properties->country : ''),

                  'osm_key' => (property_exists($val->properties, 'osm_key') ? $val->properties->osm_key : ''),
                  'osm_value' => (property_exists($val->properties, 'osm_value') ? $val->properties->osm_value : ''),
                  //'id' => (property_exists($val->properties, 'osm_id') ? $val->properties->osm_id : uniqid()),
                  //'data' => json_encode($val),
            ];
            if ($val->geometry) {
                $row['coordinates'] = [
                    'type' => $val->geometry->type,
                    'coordinates' => $val->geometry->coordinates,
                ];
            }
            //$row['id'] = json_encode($row);
            $r[]=$row;
        }
        return $r;
    }
}
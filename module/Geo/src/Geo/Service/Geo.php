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
class Geo extends AbstractClient
{
    protected function setupClient($uri)
    {
        $client = parent::setupClient($uri);
        $client->setMethod('GET');
        $client->setParameterGet(['country' => 'DE', 'zoom' => 1]);

        return $client;
    }

    protected function preQuery($term, array $params)
    {
        $query = $this->client->getRequest()->getQuery();
        $query->set('q', $term);
        $query->set('plz', 0 < (int) $term ? 1 : 0);
    }

    protected function processResult($result)
    {
        $result = json_decode($result, JSON_OBJECT_AS_ARRAY);

        $r=[];
        foreach ($result["result"] as $val) {
            $coords = $this->queryCoords($val);
            if (false !== strpos($val, ',')) {
                $parts = explode(',', $val, 2);
                $name = trim($parts[0]);
                $state = trim($parts[1]);
            } else {
                $name = $val;
                $state = null;
            }

            $r[] = [
                'name' => $name,
                'state' => $state,
                'coordinates' => $coords,
                'id' => $coords
                        ? 'c:' . $coords[0] . ':' . $coords[1]
                        : 'q:' . $val,
                'data' => json_encode($val),
            ];
        }

        return $r;
    }

    public function queryCoords($term)
    {
        $query = $this->client->getRequest()->getQuery();
        $query->set('coor', 1)->set('q', $term);

        $response = $this->client->send();
        $result = $response->getBody();
        $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (!isset($result["result"][0])) { return false; }
        $coord = $result["result"][0];
        $coord = explode(',', $coord);

        return [str_replace('.', ',', $coord[1]), str_replace('.',',',$coord[0])];
    }
}
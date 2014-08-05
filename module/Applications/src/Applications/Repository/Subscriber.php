<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Repository;

use Applications\Entity\Subscriber as SubscriberEntity;
use Core\Repository\AbstractProviderRepository;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * class for accessing a subscriber
 */
class Subscriber extends AbstractProviderRepository
{   
    protected $service;
    
    /**
     * 
     */
    public function init(ServiceLocatorInterface $serviceLocator) 
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
    
    /**
     * Find a subscriber by an uri
     * 
     * @param String $uri
     * @param boolean $create
     * @return Applications\Entity\Subscriber
     */
    public function findbyUri($uri, $create = false) {
        $subScriber = $this->findOneBy(array( "uri" => $uri ));
        if (!isset($subScriber) && $create) {
            $subScriber = $this->create();
            $subScriber->uri = $uri;
            $this->dm->persist($subScriber);
            $this->dm->flush();
        }
        return $subScriber; 
    }
    
    /**
     * Find a subscriber by an uri or create it.
     * 
     * @param unknown $uri
     * @return Applications\Entity\Subscriber
     */
    public function findbyUriOrCreate($uri)
    {
        return $this->findbyUri($uri, true);
    }

    public function getSubscriberName($uri)
    {
        // @TODO, uri must be a real place, with no regular expressions
        $services = $this->serviceLocator;
//        $config = $services->get('config');
//        $portal = 0;
//        $name = '';
//        if (preg_match('/^.*?(\d+)$/', $uri, $matches)) {
//            $portal = (int) $matches[1];
//        }
//        if ($portal == 0) {
//            //$services->get('Log/Core/Cam')->info('Applications/getSubscriberName: ' . $url . ', returned: ' . $status);
//            return '';
//        }
//        if (isset($config['Applications']) && isset($config['Applications']['getSubscriberName'])) {
//            try {
//                $url = $config['Applications']['getSubscriberName'];
//                $client = new \Zend\Http\Client($url . $portal);
//                $client->setMethod('GET');
//                $response = $client->send();
//                $status = $response->getStatusCode();
//                if ($status == 200) {
//                    $result = $response->getBody();
//                    $result = json_decode($result);
//                    if (property_exists($result, $portal)) {
//                        $name = $result->{$portal};
//                    }
//                } else {
//                    $services->get('Log/Core/Cam')->err('Applications/getSubscriberName: ' . $url . ', returned: ' . $status);
//                }
//            } catch (\Exception $e) {
//                
//            }
//        }

        $name = '';
        try {
            $client = new \Zend\Http\Client($uri);
            $client->setMethod('GET');
            $response = $client->send();
            $status = $response->getStatusCode();
            if ($status == 200) {
                $result = $response->getBody();
                $result = (array) json_decode($result);
                if (0 < count($result)) {
                    $name = array_pop($result);
                }
                //if (property_exists($result, 'portal')) {
                //    $name = $result->portal;
                //}
            } else {
                $services->get('Log/Core/Cam')->err('Applications/getSubscriberName: ' . $uri . ', returned: ' . $status);
            }
        } catch (\Exception $e) {
            
        }
        return $name;
    }
}
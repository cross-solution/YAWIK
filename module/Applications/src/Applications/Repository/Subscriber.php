<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Applications\Repository;

use Applications\Entity\Subscriber as SubscriberEntity;
use Core\Repository\AbstractProviderRepository;

/**
 * class for accessing a subsciber
 */
class Subscriber extends AbstractProviderRepository
{   
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
    public function findbyUriOrCreate($uri) {
        return $this->findbyUri($uri, true);
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Repository;

use Core\Repository\AbstractRepository;


/**
 * class for accessing a subscriber
 */
class Subscriber extends AbstractRepository
{   

    /**
     * Find a subscriber by an uri
     * 
     * @param String $uri
     * @param boolean $create
     * @return \Applications\Entity\Subscriber
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
    
}
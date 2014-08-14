<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Repository;

use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Organization extends AbstractRepository 
{
    /**
     * Find a organizations.name by an name
     * 
     * @param String $uri
     * @param boolean $create
     * @return Applications\Entity\Subscriber
     */
    public function findbyName($name, $create = false) {
        $organization = $this->findOneBy(array( "name" => $name ));
        /*
        if (!isset($subScriber) && $create) {
            $subScriber = $this->create();
            $subScriber->uri = $uri;
            $this->dm->persist($subScriber);
            $this->dm->flush();
        }
        */
        return $subScriber; 
    }
    
    
}
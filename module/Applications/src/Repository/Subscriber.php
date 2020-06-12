<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
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
    public function findByUri($uri, $create = false)
    {
        $subscriber = $this->findOneBy(array( "uri" => $uri ));
        if (!isset($subscriber) && $create) {
            $subscriber = $this->create();
            $subscriber->uri = $uri;
            $this->dm->persist($subscriber);
            $this->dm->flush();
        }
        return $subscriber;
    }
}

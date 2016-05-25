<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Entity;

use Core\Entity\EntityInterface;

/**
 * Personal informations of a subscriber. This class can translate a subscriber ID into an subscriber name
 * by calling an API of another YAWIK
 */
interface SubscriberInterface extends EntityInterface
{
    /**
     * Gets the name of the instance, who has published the job ad.
     *
     * @return String
     */
    public function getName();

    /**
     * Sets a name of the Instance, who has published the job
     *
     * @param String $name
     *
     * @return \Applications\Entity\Subscriber
     */
    public function setName($name);

    /**
     * Gets the job publishers URI
     *
     * @return String
     */
    public function getUri();

    /**
     * Sets the job publishers URI
     *
     * @param String $uri
     *
     * @return \Applications\Entity\Subscriber
     */
    public function setUri($uri);
}

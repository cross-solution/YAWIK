<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ProfileInterface.php */
namespace Auth\Entity\SocialProfiles;

use Doctrine\Common\Collections\Collection;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;

/**
 * Interface of a social network profile entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface ProfileInterface extends EntityInterface, IdentifiableEntityInterface
{
    
    /**
     * Sets the name of the profile.
     *
     * @param string $name
     * @return ProfileInterface
     */
    public function setName($name);
    
    /**
     * Gets the name of the profile.
     *
     * @return string
     */
    public function getName();
    
    /**
     * Sets the raw data (API-Result) of the profile.
     *
     * @param array $data
     * @return ProfileInterface
     */
    public function setData(array $data);
    
    /**
     * Gets the raw data of the profile.
     *
     * @param string|null $key if given, return only the data
     *                         with this key. (Dot-notation allowed)
     * @return mixed|null
     */
    public function getData($key = null);
    
    /**
     * Gets the permalink of the profile.
     *
     * @return string
     */
    public function getLink();
    
    /**
     * Gets a collection of {@link \Cv\Entity\Education} entities.
     *
     * @return Collection
     */
    public function getEducations();
    
    /**
     * Gets a collection of {@link \Cv\Entity\Employment} entities.
     *
     * @return Collection
     */
    public function getEmployments();
}

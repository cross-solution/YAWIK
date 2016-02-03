<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RatingInterface.php */
namespace Applications\Entity;

use Core\Entity\RatingInterface as CoreRatingInterface;

/**
 * Application Rating Interface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface RatingInterface extends CoreRatingInterface
{
    
    
    /**
     * Gets the rating for an application
     *
     * @return int
     */
    public function getRating();
    
    /**
     * Sets the rating for an application
     *
     * @param int $rating
     * @return RatingInterface
     */
    public function setRating($rating);
}

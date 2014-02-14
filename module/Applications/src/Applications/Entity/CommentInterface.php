<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** CommentInterface.php */ 
namespace Applications\Entity;

use Core\Entity\IdentifiableEntityInterface;

/**
 * Application comment interface
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface CommentInterface extends IdentifiableEntityInterface
{
    
    /**
     * Gets the comment message
     * 
     * @return string
     */
    public function getMessage();
    
    /**
     * Sets the comment message
     * 
     * @param string $message
     * @return CommentInterface
     */
    public function setMessage($message);
    
    /**
     * Gets this comment's application rating
     * 
     * @return RatingInterface
     */
    public function getRating();
    
    /**
     * Sets this comment's application rating
     * 
     * @param RatingInterface $rating
     * @return CommentInterface
     */
    public function setRating(RatingInterface $rating);
}


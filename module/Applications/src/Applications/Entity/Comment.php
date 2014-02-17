<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Comment.php */ 
namespace Applications\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Application comment entity.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class Comment extends AbstractIdentifiableEntity implements CommentInterface
{
    
    /**
     * User this comment belongs to
     * 
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true) 
     */
    protected $user;
    
    /**
     * Comment message
     * 
     * @var string
     * @ODM\String
     */
    protected $message;
    
    /**
     * Application rating
     * 
     * @var Rating
     * @ODM\EmbedOne(targetDocument="Rating")
     */
    protected $rating;
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::getUser()
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * @{inheritDoc}
     * 
     * @return Comment
     * @see \Applications\Entity\CommentInterface::setUser()
     */
	public function setUser (UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

	/**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::getMessage()
     */
    public function getMessage ()
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::setMessage()
     * @return Comment
     */
    public function setMessage ($message)
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::getRating()
     */
    public function getRating ()
    {
        if (!isset($this->rating)) {
            $this->setRating(new Rating());
        }
        return $this->rating;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::setRating()
     */
    public function setRating (RatingInterface $rating)
    {
        $this->rating = $rating;
        return $this;
    }

    
    
}


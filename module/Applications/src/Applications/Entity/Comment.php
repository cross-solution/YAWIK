<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
 * @ODM\EmbeddedDocument @ODM\HasLifecycleCallbacks
 *
 */
class Comment extends AbstractIdentifiableEntity implements CommentInterface
{
    
    /**
     * User this comment belongs to
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id")
     */
    protected $user;
    
    /**
     * Created date
     *
     * @var \DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateCreated;
    
    /**
     * Modification date
     *
     * @var \DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateModified;
    
    /**
     * Comment message
     *
     * @var string
     * @ODM\Field(type="string")
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @{inheritDoc}
     *
     * @return $this
     * @see \Applications\Entity\CommentInterface::setUser()
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $date)
    {
        $this->dateCreated = $date;
        return $this;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDateModified(\DateTime $date)
    {
        $this->dateModified = $date;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::getMessage()
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::setMessage()
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::getRating()
     */
    public function getRating()
    {
        if (!isset($this->rating)) {
            $this->setRating(new Rating());
        }
        return $this->rating;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\CommentInterface::setRating()
     * @return $this
     */
    public function setRating(RatingInterface $rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /** @ODM\PreUpdate */
    public function preUpdate()
    {
        $this->setDateModified(new \DateTime());
    }
    
    /** @ODM\PrePersist */
    public function prePersist()
    {
        $this->setDateCreated(new \DateTime());
    }
}

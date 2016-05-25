<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Rating.php */
namespace Applications\Entity;

use Core\Entity\AbstractRatingEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Holds all ratings for an application
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class Rating extends AbstractRatingEntity implements RatingInterface
{
    /**
     * Application rating value
     * @var int
     * @ODM\Field(type="int")
     */
    protected $rating;
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\RatingInterface::getRating()
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * {@inheritDoc}
     * @see \Applications\Entity\RatingInterface::setRating()
     * @return Rating
     */
    public function setRating($rating)
    {
        $this->rating = (int) $rating;
        return $this;
    }
}

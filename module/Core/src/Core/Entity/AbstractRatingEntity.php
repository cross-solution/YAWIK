<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AbstractRatingEntity.php */
namespace Core\Entity;

/**
 * Base rating entity.
 *
 * Implements common and helper methods for Rating Entities.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
abstract class AbstractRatingEntity extends AbstractEntity implements RatingInterface
{
    /**
     * Average rating.
     * This is not mapped by doctrine!
     * @var int;
     */
    protected $_average;
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\RatingInterface::getAverage()
     */
    public function getAverage($includeNoneRating = false, $recalculate = false)
    {
        if ($this->_average && !$recalculate) {
            return $this->_average;
        }
        
        $sum   = 0;
        $count = 0;
        foreach (get_class_methods($this) as $method) {
            if ('getAverage' == $method || 0 !== strpos($method, 'get')) {
                continue;
            }
            
            $rating = $this->$method();
            if (!$this->checkRatingValue($rating, /*throwException*/ false)
                || (!$includeNoneRating && $rating == static::RATING_NONE)) {
                continue;
            }
            
            $sum   += $rating;
            $count += 1;
        }
        
        $average = 0 == $count ? 0 : round($sum / $count);
        $this->_average = $average;
        
        return $average;
    }
    
    /**
     * Checks if rating is a valid value.
     *
     * @param int $rating
     * @param bool $throwException
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function checkRatingValue($rating, $throwException = true)
    {
        if (!is_int($rating) || static::RATING_EXCELLENT < $rating || static::RATING_NONE > $rating) {
            if ($throwException) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid rating value.', $rating));
            }
            return false;
        }
        return true;
    }
}

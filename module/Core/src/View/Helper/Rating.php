<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helpers */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Core\Entity\RatingInterface;

/**
 * Renders a visual representation of a rating value.
 *
 * <code>
 *
 *      // Renders a compact rating bar representation:
 *      echo $this->rating(3);
 *
 *      // Renders a wide rating bar representation:
 *      echo $this->rating(4, 'wider');
 *
 *      // Pass an rating interface
 *      $rating = $entity->getRating();
 *      echo $this->rating($rating);
 * </code>
 *
 * @see \Core\Entity\Rating
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Rating extends AbstractHelper
{

    /**
     * Maps rating values to text.
     *
     * @var array
     */
    protected static $ratingValueMap = array(
        RatingInterface::RATING_NONE      => 'Not Rated',
        RatingInterface::RATING_POOR      => 'Poor',
        RatingInterface::RATING_BAD       => 'Bad',
        RatingInterface::RATING_AVERAGE   => 'Average',
        RatingInterface::RATING_GOOD      => 'Good',
        RatingInterface::RATING_EXCELLENT => 'Excellent',
    );
    
    
    /**
     * generates a rating bar from a rating value
     *
     * @param int|RatingInterface $rating
     * @param string $mode Rendering mode:
     *                     - "compact": renders a densed rating presentation.
     *                     - ANY STRING: renders a wide rating presentation.
     * @return string
     */
    public function __invoke($rating, $mode = 'compact')
    {
        if ($rating instanceof RatingInterface) {
            $rating = $rating->getAverage();
        }
        
        $output = '<div class="br-widget br-readonly '
                . ('compact' == $mode ? ' br-compact' : '')
                . '" title="' . $this->getView()->translate(self::$ratingValueMap[$rating]) . '">';
        for ($i=1; $i<6; $i++) {
            $class = $i <= $rating ? 'br-selected' : '';
            $class .= $i == $rating ? ' br-current' : '';
            
            $output .= '<a class="' . $class . '"></a>';
        }
        $output .= '</div>';
        
        return $output;
    }
}

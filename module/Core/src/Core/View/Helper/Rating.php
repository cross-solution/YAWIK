<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Core\Entity\RatingInterface;


class Rating extends AbstractHelper {

    
    
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
     * @param string $gender
     * @return string
     */
    public function __invoke($rating, $mode = 'compact')
    {
        if ($rating instanceOf RatingInterface) {
            $rating = $rating->getAverage();
        }
        
        $output = '<div class="br-widget br-readonly ' 
                . ('compact' == $mode ? ' br-compact' : '' )
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


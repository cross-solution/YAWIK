<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Period extends AbstractHelper
{

    /**
     * returns the number of years of the education or work experience
     *
     * @param array $array
     * @return string
     */
    public function __invoke($array)
    {
        // calculate EndDate - StartDate = X Years.
        // eg. 4.2 Years
        
        $days=0;
        foreach ($array as $obj) {
            $date1 = new \DateTime($obj->getEndDate());
            $date2 = new \DateTime($obj->getStartDate());
            $interval = $date1->diff($date2);
            $days+=abs($interval->format('%R%a'));
        }
        return round($days/365, 1);
    }
}

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


class Period extends AbstractHelper {

	/**
	 * returns the number of years of the education or work experience
	 *
	 * @param string $obj
	 * @return string
	 */
	public function __invoke($array)
	{
		// calculate EndDate - StartDate = X Years. 
		// eg. 4.2 Years
		
		$days=0;
		foreach($array as $obj){
			$date1 = new \DateTime($obj->endDate);
			$date2 = new \DateTime($obj->startDate);
			$interval = $date1->diff($date2);
			$days+=abs($interval->format('%R%a'));
		}
		return round($days/365,1);
	}
}
?>
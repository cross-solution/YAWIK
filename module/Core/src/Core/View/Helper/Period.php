<?php
/**
 * Cross Applicant Management
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
	public function __invoke($obj)
	{
		// calculate EndDate - StartDate = X Years. 
		// eg. 4.2 Years
		return "write this view Helper";
	}
}

?>
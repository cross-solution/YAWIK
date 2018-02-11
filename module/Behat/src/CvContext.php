<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;


use Behat\Behat\Context\Context;

/**
 * Class CvContext
 *
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since   0.29
 * @package Yawik\Behat
 */
class CvContext implements Context
{
	use CommonContextTrait;
	
	
	/**
	 * @Given I go to manage my resume page
	 */
	public function iGoToManageResumePage()
	{
	    $url = $this->buildUrl('lang/my-cv');
		$this->visit($url);
	}
	
	/**
	 * @When I click edit on my personal information
	 */
	public function iClickEditOnPersonalInformations()
	{
		$this->summaryFormContext->iClickEditOnForm('resumePersonalInformations');
	}
}

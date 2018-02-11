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

class SettingsContext implements Context
{
	use CommonContextTrait;
	
	/**
	 * @Given I go to settings page
	 */
	public function iGoToSettingsPage()
	{
		//$this->visit('/settings');
        $url = $this->buildUrl('lang/settings');
        $this->visit($url);
	}
	
	/**
	 * @Given I go to email template settings page
	 */
	public function iGoToEmailTemplatePage()
	{
	    $url = $this->buildUrl('lang/settings',[
	        'module' => 'Applications'
        ]);
		$this->visit($url);
	}
}

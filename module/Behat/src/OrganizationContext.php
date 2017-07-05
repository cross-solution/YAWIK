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
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Class OrganizationContext
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.29
 * @package Yawik\Behat
 */
class OrganizationContext implements Context
{
	use CommonContextTrait;
	
	private $elementMap = array(
		'name' => '#sf-nameForm',
		'location' => '#sf-locationForm',
		'employees' => '#sf-employeesManagement',
		'workflow' => '#sf-workflowSettings',
	);
	
	/**
	 * @Given I go to my organization page
	 */
	public function iGoToMyOrganizationPage()
	{
		$url = $this->coreContext->generateUrl('/en/my/organization');
		$this->coreContext->iVisit($url);
	}
	
	/**
	 * @When I hover over name form
	 */
	public function iMouseOverOrganizationNameForm()
	{
		$locator = '#sf-nameForm .sf-summary';
		$this->coreContext->iHoverOverTheElement($locator);
	}
	
	/**
	 * @When I click edit on :name form
	 */
	public function iClickEditOnForm($name)
	{
		$this->iClickForm($name);
		$type = $this->elementMap[$name];
		$locator = $type.' .sf-summary .sf-controls button';
		$element = $this->minkContext->getSession()->getPage()->find('css',$locator);
		$element->click();
	}
	
	/**
	 * @When I click :form form
	 */
	public function iClickForm($name)
	{
		$type = $this->elementMap[$name];
		$locator = $type.' .sf-summary';
		$session = $this->minkContext->getSession();
		$script = <<<EOC
var tElement = jQuery("$locator .sf-controls");
tElement.css('display','block');
tElement.css('visibility','visible');
EOC;
		$session->executeScript($script);
	}
	
	/**
	 * @When I save :type form
	 */
	public function iSaveLocationForm($type)
	{
		$locator = $this->elementMap[$type].' button.sf-submit';
		$element = $this->minkContext->getSession()->getPage()->find('css',$locator);
		$element->click();
	}
	
	/**
	 * @Given I go to create new organization page
	 */
	public function iGoToCreateNewOrganizationPage()
	{
		$this->visit('/en/organizations/edit');
	}
	
	/**
	 * @Given I go to organization overview page
	 */
	public function iGoToOrganizationOverviewPage()
	{
		$this->visit('/en/organizations');
	}
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Core\Repository\RepositoryService;
use Zend\Mvc\Application;


/**
 * Class FeatureContext
 * @package Yawik\Behat
 */
class CoreContext extends RawMinkContext
{
	static protected $application;
	
	/**
	 * @var MinkContext
	 */
	protected $minkContext;
	
	/**
	 * @BeforeScenario
	 * @param BeforeScenarioScope $scope
	 */
	public function gatherContexts(BeforeScenarioScope $scope)
	{
		$this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
	}
	
	/**
	 * @return Application
	 */
	public function getApplication()
	{
		if(!is_object(static::$application)){
			$configFile = realpath(__DIR__.'/../../../config/config.php');
			$config = include($configFile);
			static::$application = Application::init($config);
		}
		return static::$application;
	}
	
	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->getApplication()->getServiceManager();
	}
	
	/**
	 * @return \Zend\EventManager\EventManagerInterface
	 */
	public function getEventManager()
	{
		return $this->getApplication()->getEventManager();
	}
	
	/**
	 * @return RepositoryService
	 */
	public function getRepositories()
	{
		return $this->getServiceManager()->get('repositories');
	}
	
	/**
	 * @param $name
	 * @param array $params
	 *
	 * @return string
	 */
	public function generateUrl($name)
	{
		return $this->minkContext->locatePath($name);
	}
	
	/**
	 * @When /^I hover over the element "([^"]*)"$/
	 */
	public function iHoverOverTheElement($locator)
	{
		$session = $this->minkContext->getSession(); // get the mink session
		$element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element
		
		// errors must not pass silently
		if (null === $element) {
			throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
		}
		
		// ok, let's hover it
		$element->mouseOver();
	}
	
	/**
	 * @Given /^I wait for (\d+) seconds$/
	 */
	public function iWaitForSecond($second)
	{
		sleep($second);
	}
	
	/**
	 * @Then /^I wait for the ajax response$/
	 */
	public function iWaitForTheAjaxResponse()
	{
		$this->getSession()->wait(5000, '(0 === jQuery.active)');
	}
	
	/**
	 * Some forms do not have a Submit button just pass the ID
	 *
	 * @Given /^I submit the form with id "([^"]*)"$/
	 */
	public function iSubmitTheFormWithId($arg)
	{
		$node = $this->minkContext->getSession()->getPage()->find('css', $arg);
		if($node) {
			$this->minkContext->getSession()->executeScript("jQuery('$arg').submit();");
		} else {
			throw new \Exception('Element not found');
		}
	}
	
	/**
	 * @Then I switch to popup :name
	 *
	 * @param $name
	 */
	public function iSwitchToPopup($name)
	{
		$this->iSetMainWindowName();
		$this->getSession()->switchToWindow($name);
	}
	
	/**
	 * @Then I set main window name
	 */
	public function iSetMainWindowName()
	{
		$window_name = 'main_window';
		$script = 'window.name = "' . $window_name . '"';
		$this->getSession()->executeScript($script);
	}
	
	/**
	 * @Then I switch back to main window
	 */
	public function iSwitchBackToMainWindow()
	{
		$this->getSession()->switchToWindow('main_window');
	}
	
	public function iVisit($url)
	{
		$this->minkContext->getSession()->visit($url);
	}
}
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
use Jobs\Repository\Categories;
use Zend\Mvc\Application;


/**
 * Class FeatureContext
 * @package Yawik\Behat
 */
class CoreContext extends RawMinkContext
{
    use CommonContextTrait;

	static protected $application;
	
	static private $jobCategoryChecked = false;
	
	/**
	 * @BeforeScenario
	 * @param BeforeScenarioScope $scope
	 */
	public function setupContexts(BeforeScenarioScope $scope)
	{
		if(false === static::$jobCategoryChecked){
			/* @var Categories $catRepo */
			$catRepo = $this->getRepositories()->get('Jobs/Category');
			$all = $catRepo->findAll();
			if(count($all) <= 1){
				$catRepo->createDefaultCategory('professions');
				$catRepo->createDefaultCategory('industries');
				$catRepo->createDefaultCategory('employmentTypes');
			}
			static::$jobCategoryChecked = true;
		}
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
	
	/**
	 * @When I scroll :selector into view
	 *
	 * @param string $selector Allowed selectors: #id, .className, //xpath
	 *
	 * @throws \Exception
	 */
	public function scrollIntoView($selector)
	{
		$locator = substr($selector, 0, 1);
		
		switch ($locator) {
			case '/' : // XPath selector
				$function = <<<JS
(function(){
  var elem = document.evaluate($selector, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
  elem.scrollIntoView(false);
})()
JS;
				break;
			
			case '#' : // ID selector
				$selector = substr($selector, 1);
				$function = <<<JS
(function(){
  var elem = document.getElementById("$selector");
  elem.scrollIntoView(false);
})()
JS;
				break;
			
			case '.' : // Class selector
				$selector = substr($selector, 1);
				$function = <<<JS
(function(){
  var elem = document.getElementsByClassName("$selector");
  elem[0].scrollIntoView(false);
})()
JS;
				break;
			
			default:
				throw new \Exception(__METHOD__ . ' Couldn\'t find selector: ' . $selector . ' - Allowed selectors: #id, .className, //xpath');
				break;
		}
		
		try {
			$this->getSession()->executeScript($function);
		} catch (\Exception $e) {
			throw new \Exception(__METHOD__ . ' failed'. ' Message: for this locator:"'.$selector.'"');
		}
	}
	
	/**
	 * @When I click location selector
	 */
	public function iClickLocationSelector()
	{
		$locator = '#jobBase-geoLocation-span .select2';
		$element = $this->getElement($locator);
		$element->click();
	}
	
	/**
	 * @param $locator
	 * @param string $selector
	 *
	 * @return \Behat\Mink\Element\NodeElement|mixed|null
	 */
	public function getElement($locator,$selector='css')
	{
		$page = $this->minkContext->getSession()->getPage();
		$element = $page->find('css',$locator);
		return $element;
	}
	
	/**
	 * @When I fill in location search with :term
	 * @param $term
	 */
	public function iFillInLocationSearch($term)
	{
		$locator = '.select2-container--open .select2-search__field';
		$element = $this->getElement($locator);
		$element->focus();
		$element->setValue($term);
		$this->iWaitForTheAjaxResponse();
	}
	
	public function iClickOn()
	{
	
	}
	
	/**
	 * Click some text
	 *
	 * @When /^I click on the text "([^"]*)"$/
	 */
	public function iClickOnTheText($text)
	{
		$session = $this->getSession();
		$element = $session->getPage()->find(
			'xpath',
			$session->getSelectorsHandler()->selectorToXpath('xpath', '*//*[text()="'. $text .'"]')
		);
		if(null === $element){
			$element = $session->getPage()->find(
				'named',
				array('id',$text)
			);
		}
		if (null === $element) {
			throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
		}
		
		$element->click();
		
	}

    /**
     * @Then /^(?:|I )should see translated text "(?P<text>(?:[^"]|\\")*)"$/
     */
	public function iShouldSeeText($text)
    {
        $translator = $this->getServiceManager()->get('translator');
        $translated = $translator->translate($text);
        $this->minkContext->assertSession()->pageTextContains($translated);
    }

    /**
     * @When I go to dashboard page
     */
    public function iGoToDashboardPage()
    {
        $url = $this->buildUrl('lang/dashboard');
        $this->iVisit($url);
    }
}

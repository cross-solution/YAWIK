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
use Behat\Mink\Element\DocumentElement;
use Behat\MinkExtension\Context\RawMinkContext;
use WebDriver\Element;

/**
 * Class Select2Context
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Yawik\Behat
 * @since 0.29
 */
class Select2Context extends RawMinkContext implements Context
{
	protected $timeout = 5;
	
	/**
	 * Fills in Select2 field with specified
	 *
	 * @When /^(?:|I )fill in select2 "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
	 * @When /^(?:|I )fill in select2 "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
	 */
	public function iFillInSelect2Field($field, $value)
	{
		$page = $this->getSession()->getPage();
		
		$this->openField($page, $field);
		$this->selectValue($page, $field, $value, $this->timeout);
	}
	
	/**
	 * @When I fill in select2 search :field with :search and I choose :choice
	 * @param $field
	 * @param $value
	 */
	public function iFillInSelect2FieldWith($field,$search,$choice=null)
	{
		$page = $this->getSession()->getPage();
		$this->openField($page, $field);
		$this->fillSearchField($page,$field,$search);
		$this->selectValue($page, $field, $choice);
	}
	
	/**
	 * Fill Select2 search field
	 *
	 * @param DocumentElement $page
	 * @param string          $field
	 * @param string          $value
	 * @throws \Exception
	 */
	private function fillSearchField(DocumentElement $page, $field, $value)
	{
		$driver = $this->getSession()->getDriver();
		if ('Behat\Mink\Driver\Selenium2Driver' === get_class($driver)) {
			// Can't use `$this->getSession()->getPage()->find()` because of https://github.com/minkphp/MinkSelenium2Driver/issues/188
			
			$element = $page->find('css','.select2-container--open .select2-search__field');
			$xpath = $element->getXpath();
			$select2Input = $this->getSession()
				->getDriver()
				->getWebDriverSession()
				->element('xpath',$xpath)
				//->element('xpath', "//html/descendant-or-self::*[@class and contains(concat(' ', normalize-space(@class), ' '), ' select2-search__field ')]")
			;
			if (!$select2Input) {
				throw new \Exception(sprintf('No field "%s" found', $field));
			}
			
			$select2Input->postValue(['value' => [$value]]);
		} else {
			$select2Input = $page->find('css', '.select2-search__field');
			if (!$select2Input) {
				throw new \Exception(sprintf('No input found for "%s"', $field));
			}
			$select2Input->setValue($value);
		}
		
		$this->waitForLoadingResults($this->timeout);
	}
	
	/**
	 * Select value in choice list
	 *
	 * @param DocumentElement $page
	 * @param string          $field
	 * @param string          $value
	 * @param int             $time
	 * @throws \Exception
	 */
	private function selectValue(DocumentElement $page, $field, $value, $time=5)
	{
		$this->waitForLoadingResults($time);
		
		$chosenResults = $page->findAll('css', '.select2-results li');
		foreach ($chosenResults as $result) {
			$text = $result->getText();
			if (false!==strpos($text,$value)) {
				$result->click();
				return;
			}
		}
		
		throw new \Exception(sprintf('Value "%s" not found for "%s"', $value, $field));
	}
	
	private function openField(DocumentElement $page, $field)
	{
		$inputField = $page->find('css',$field);
		if(!$inputField){
			$fieldName = sprintf('select[name="%s"] + .select2-container', $field);
			$inputField = $page->find('css', $fieldName);
		}
		if (!$inputField) {
			throw new \Exception(sprintf('No field "%s" found', $field));
		}
		
		$choice = $inputField->find('css', '.select2-selection');
		if (!$choice) {
			throw new \Exception(sprintf('No select2 choice found for "%s"', $field));
		}
		$choice->press();
	}
	
	/**
	 * Wait the end of fetching Select2 results
	 *
	 * @param int $time Time to wait in seconds
	 */
	private function waitForLoadingResults($time)
	{
		for ($i = 0; $i < $time; $i++) {
			if (!$this->getSession()->getPage()->find('css', '.select2-results__option.loading-results')) {
				return;
			}
			
			sleep(1);
		}
	}
	
}
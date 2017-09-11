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
use Doctrine\Common\Util\Inflector;

class SummaryFormContext implements Context
{
	use CommonContextTrait;
	
	private $elementMap = array(
		'name' => '#sf-nameForm',
		'location' => '#sf-locationForm',
		'employees' => '#sf-employeesManagement',
		'workflow' => '#sf-workflowSettings',
		'jobTitleAndLocation' => '#general-locationForm',
		'jobClassification' => '#sf-general-classifications',
		'customerNote' => '#sf-general-customerNote',
		'personalInformations' => '#sf-contact-contact',
		'resumePersonalInformations' => '#sf-contact',
	);
	
	/**
	 * @When I click edit on :name form
	 * @TODO: [ZF3] move this method to CoreContext
	 */
	public function iClickEditOnForm($name)
	{
		$this->iClickForm($name);
		$name = Inflector::camelize($name);
		$type = $this->elementMap[$name];
		$locator = $type.' .sf-summary .sf-controls button';
		$element = $this->minkContext->getSession()->getPage()->find('css',$locator);
		if(!$element){
			throw new \Exception('No element found with this locator: "'.$locator.'"');
		}
		$element->click();
	}
	
	/**
	 * @When I click :form form
	 */
	public function iClickForm($name)
	{
		$name = Inflector::camelize($name);
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
	public function iSaveForm($type)
	{
		$type = Inflector::camelize($type);
		$locator = $this->elementMap[$type].' button.sf-submit';
		$this->coreContext->scrollIntoView($locator);
		$element = $this->minkContext->getSession()->getPage()->find('css',$locator);
		$element->click();
	}
}
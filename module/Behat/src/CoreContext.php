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
			static::$application = Application::init(include $configFile)->bootstrap();
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
}
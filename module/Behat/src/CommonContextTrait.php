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
use Core\Repository\RepositoryInterface;

trait CommonContextTrait
{
	/**
	 * @var MinkContext
	 */
	protected $minkContext;
	
	/**
	 * @var CoreContext
	 */
	protected $coreContext;
	
	/**
	 * @var UserContext
	 */
	protected $userContext;
	
	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 */
	public function gatherContexts(BeforeScenarioScope $scope)
	{
		$this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
		$this->coreContext = $scope->getEnvironment()->getContext(CoreContext::class);
		$this->userContext = $scope->getEnvironment()->getContext(UserContext::class);
	}
	
	public function generateUrl($url)
	{
		return $this->coreContext->generateUrl($url);
	}
	
	public function visit($url)
	{
		$this->coreContext->iVisit($this->generateUrl($url));
	}
	
	/**
	 * @param $id
	 * @return mixed|object
	 */
	public function getService($id)
	{
		return $this->coreContext->getServiceManager()->get($id);
	}
	
	/**
	 * @param $id
	 *
	 * @return RepositoryInterface
	 */
	public function getRepository($id)
	{
		return $this->coreContext->getRepositories()->get($id);
	}
	
	/**
	 * @return \Auth\Entity\User
	 */
	public function getCurrentUser()
	{
		return $this->userContext->getCurrentUser();
	}
}
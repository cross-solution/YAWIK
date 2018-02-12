<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;


use Auth\Entity\User;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Core\Repository\RepositoryInterface;
use Zend\View\Helper\Url;

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
	 * @var SummaryFormContext
	 */
	protected $summaryFormContext;
	
	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 */
	final public function gatherContexts(BeforeScenarioScope $scope)
	{
		$this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
		$this->coreContext = $scope->getEnvironment()->getContext(CoreContext::class);
		$this->userContext = $scope->getEnvironment()->getContext(UserContext::class);
		$this->summaryFormContext = $scope->getEnvironment()->getContext(SummaryFormContext::class);
	}
	
	public function buildUrl($name, array $params=array(), array $options=array())
	{
	    $defaults = ['lang'=>'en'];
	    $params = array_merge($defaults,$params);
        /* @var Url $urlHelper */
        $urlHelper = $this
            ->getService('ViewHelperManager')
            ->get('url')
        ;
        $url = $urlHelper($name,$params,$options);

        return $this->coreContext->generateUrl($url);
	}
	
	public function visit($url)
	{
		$this->coreContext->iVisit($url);
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
     * @return UserContext
     */
	public function getUserContext()
    {
        return $this->userContext;
    }
}

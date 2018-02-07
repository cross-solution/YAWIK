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
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Util\Inflector;
use Organizations\Entity\Organization;
use Yawik\Behat\Exception\FailedExpectationException;
use Organizations\Repository\Organization as OrganizationRepository;

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

    /**
     * @var JobContext
     */
	private $jobContext;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function setupContext(BeforeScenarioScope $scope)
    {
        $this->jobContext = $scope->getEnvironment()->getContext(JobContext::class);
    }

	/**
	 * @Given I go to my organization page
	 */
	public function iGoToMyOrganizationPage()
	{
        $url = $this->generateUrl('lang/my-organization');
		$this->visit($url);
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
	 * @Given I go to create new organization page
	 */
	public function iGoToCreateNewOrganizationPage()
	{
		//$this->visit('/organizations/edit');
        $url = $this->generateUrl('lang/organizations/edit');
        $this->visit($url);
	}
	
	/**
	 * @Given I go to organization overview page
	 */
	public function iGoToOrganizationOverviewPage()
	{
		//$this->visit('/organizations');
		$url = $this->generateUrl('lang/organizations');
		$this->visit($url);
	}

    /**
     * @Given I have organization :name
     * @Given I have organization :name with published jobs:
     *
     * @param string $name
     * @param TableNode|null $table
     */
	public function iHaveOrganization($name, TableNode $table = null)
    {
        $userContext = $this->getUserContext();
        $user = $userContext->thereIsAUserIdentifiedBy(
            'recruiter@example.com',
            'test',
            User::ROLE_RECRUITER,
            'Test Recruiter',
            $name
        );

        if(!is_null($table)){
            foreach($table->getColumnsHash() as $index=>$definitions){
                $definitions['user'] = $user->getLogin();
                $this->jobContext->buildJob('published',$definitions);
            }
        }
    }

    /**
     * @Given I define contact for :organization organization with:
     * @param TableNode $table
     */
    public function iDefineContactWith($name, TableNode $table)
    {
        $organization = $this->findOrganizationByName($name);
        $contact = $organization->getContact();

        $definitions = $table->getRowsHash();
        foreach($definitions as $name=>$value){
            $field = Inflector::camelize($name);
            $method = 'set'.$field;
            $callback = array($contact,$method);
            if(is_callable($callback)){
                call_user_func_array($callback,[$value]);
            }
        }
        $this->getRepository('Organizations/Organization')->store($organization);
    }

    /**
     * @Given I go to profile page for organization :name
     * @param string $name
     * @throws FailedExpectationException
     */
    public function iGoToOrganizationProfilePage($name)
    {
        $organization = $this->findOrganizationByName($name);
        $url = $this->generateUrl('lang/organization-profile',[
            'id' => $organization->getId()
        ]);

        $this->visit($url);
    }

    /**
     * @param string $name
     * @return Organization
     * @throws FailedExpectationException
     */
    public function findOrganizationByName($name)
    {
        /* @var OrganizationRepository $repo */
        $repo = $this->getRepository('Organizations/Organization');
        $result = $repo->findByName($name);
        $organization = count($result) > 0 ? $result[0]:null;
        if(!$organization instanceof Organization){
            throw new FailedExpectationException(
                sprintf('Organization %s is not found.',$name)
            );
        }
        return $organization;
    }
}

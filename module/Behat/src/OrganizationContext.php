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
use Behat\Gherkin\Node\TableNode;
use Core\Entity\Permissions;
use Doctrine\Common\Util\Inflector;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Organizations\Entity\OrganizationReference;
use Yawik\Behat\Exception\FailedExpectationException;
use Organizations\Repository\Organization as OrganizationRepository;
use Jobs\Repository\Job as JobRepository;

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
        $url = $this->buildUrl('lang/my-organization');
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
        $url = $this->buildUrl('lang/organizations/edit');
        $this->visit($url);
	}
	
	/**
	 * @Given I go to organization overview page
	 */
	public function iGoToOrganizationOverviewPage()
	{
		//$this->visit('/organizations');
		$url = $this->buildUrl('lang/organizations');
		$this->visit($url);
	}

    /**
     * @Given I want to see list organization profiles
     */
	public function iWantToSeeListOrganizationProfiles()
    {
       $url = $this->buildUrl('lang/organizations/profile');
       $this->visit($url);
    }

    /**
     * @Given I have organization :name
     *
     * @internal param string $name
     * @internal param TableNode|null $table
     */
	public function iHaveOrganization($name)
    {
        $user = $this->getUserContext()->getCurrentUser();
        $organization = $this->findOrganizationByName($name,false);
        $repo = $this->getRepository('Organizations/Organization');
        if(!$organization instanceof Organization){

            $organization = new Organization();
            $organizationName = new OrganizationName($name);
            $organization->setOrganizationName($organizationName);
            $organization->setIsDraft(false);
        }
        /* @var OrganizationReference $orgReference */
        $orgReference = $user->getOrganization();
        $parent = $orgReference->getOrganization();
        $organization->setParent($parent);
        $organization->setProfileSetting(Organization::PROFILE_ALWAYS_ENABLE);
        $permissions = $organization->getPermissions();
        $permissions->grant($user,Permissions::PERMISSION_ALL);

        $repo->store($organization);
        $repo->getDocumentManager()->refresh($organization);
        $repo->getDocumentManager()->refresh($user);
    }

    /**
     * @Given organization :name have jobs:
     */
    public function organizationHavePublishedJob($name,TableNode $table)
    {
        $user = $this->getUserContext()->getCurrentUser();
        if(is_null($user)){
            throw new FailedExpectationException('Need to login first');
        }

        $organization = $this->findOrganizationByName($name);
        foreach($table->getColumnsHash() as $index=>$definitions){
            $definitions['user'] = $user->getLogin();
            $status = isset($definitions['status']) ? $definitions['status']:'draft';
            unset($definitions['status']);
            $this->jobContext->buildJob($status,$definitions,$organization);
        }
    }

    /**
     * @Given profile setting for :name is :setting
     * @param $name
     * @param $setting
     */
    public function profileSetting($name,$setting)
    {
        $repo = $this->getRepository('Organizations/Organization');
        $organization = $this->findOrganizationByName($name);

        $organization->setProfileSetting($setting);
        $repo->store($organization);
        $repo->getDocumentManager()->refresh($organization);
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
     * @Given I go to profile page for my organization
     * @param string $name
     * @throws FailedExpectationException
     */
    public function iGoToOrganizationProfilePage($name=null)
    {
        if(is_null($name)){
            $organization = $this->getUserContext()->getCurrentUser()->getOrganization()->getOrganization();
        }else{
            $organization = $this->findOrganizationByName($name);
        }
        $url = $this->buildUrl('lang/organizations/profileDetail',[
            'id' => $organization->getId()
        ]);

        $this->visit($url);
    }

    /**
     * @param string $name
     * @return Organization
     * @throws FailedExpectationException
     */
    public function findOrganizationByName($name, $throwException = true)
    {
        /* @var OrganizationRepository $repo */
        $repo = $this->getRepository('Organizations/Organization');
        $result = $repo->findByName($name);
        $organization = count($result) > 0 ? $result[0]:null;
        if(!$organization instanceof Organization && $throwException){
            throw new FailedExpectationException(
                sprintf('Organization %s is not found.',$name)
            );
        }
        return $organization;
    }

    /**
     * @Given organization :name have no job
     *
     * @param string $name
     */
    public function organizationHaveNoJob($name)
    {
        $org = $this->findOrganizationByName($name);

        /* @var JobRepository $jobRepo */
        $jobRepo = $this->getRepository('Jobs/Job');
        $result = $jobRepo->findByOrganization($org->getId());

        foreach($result as $job){
            $jobRepo->remove($job,true);
        }
    }

    /**
     * @Given I want to edit my organization
     */
    public function iWantToEditMyOrganization()
    {
        $user = $this->getUserContext()->getCurrentUser();
        $organization = $user->getOrganization()->getOrganization();
        $url = $this->buildUrl('lang/organizations/edit',['id' => $organization->getId()]);
        $this->visit($url);
    }

    /**
     * @Given I attach logo from file :file
     * @param $file
     */
    public function iAttachLogoFromFile($file)
    {
        $elementId = 'organizationLogo-original';
        $this->minkContext->attachFileToField($elementId,$file);
    }

    /**
     * @Given I remove logo from organization
     */
    public function iRemoveLogoFromOrganization()
    {
        $elementId = '#organizationLogo-original-delete';
        $element = $this->minkContext->getSession()->getPage()->find('css',$elementId);
        $element->click();
    }
}

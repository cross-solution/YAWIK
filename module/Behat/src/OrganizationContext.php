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
     */
	public function iHaveOrganization($name)
    {
        $userContext = $this->getUserContext();
        $userContext->thereIsAUserIdentifiedBy(
            'recruiter@example.com',
            'test',
            User::ROLE_RECRUITER,
            'Test Recruiter',
            $name
        );
    }

    /**
     * @Given I go to profile page for organization :name
     * @param string $name
     * @throws FailedExpectationException
     */
    public function iGoToOrganizationProfilePage($name)
    {
        /* @var OrganizationRepository $repo */
        $repo = $this->getRepository('Organizations/Organization');
        $result = $repo->findByName($name);
        $organization = $result[0];
        if(!$organization instanceof Organization){
            throw new FailedExpectationException(
                sprintf('Organization %s is not found.',$name)
            );
        }

        $url = $this->generateUrl('lang/organization-profile',[
            'id' => $organization->getId()
        ]);

        $this->visit($url);
    }
}

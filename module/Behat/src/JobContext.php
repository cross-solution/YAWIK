<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Auth\Entity\Status;
use Auth\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Core\Entity\Permissions;
use Doctrine\Common\Util\Inflector;
use Documents\UserRepository;
use Geo\Service\Photon;
use Jobs\Entity\Classifications;
use Jobs\Entity\Job;
use Jobs\Entity\Location;
use Jobs\Entity\StatusInterface;
use Jobs\Repository\Categories as CategoriesRepo;
use Jobs\Repository\Job as JobRepository;
use Zend\Json\Json;

/**
 * Class JobContext
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Yawik\Behat
 */
class JobContext implements Context
{
	use CommonContextTrait;
	
	/**
	 * @var Select2Context
	 */
	private $select2Context;
	
	/**
	 * @var Job
	 */
	private $currentJob;
	
	/**
	 * @var JobRepository
	 */
	static private $jobRepo;
	
	/**
	 * @param User $user
	 */
	static public function removeJobByUser(User $user)
	{
		$repo = static::$jobRepo;
		$results = $repo->findBy(['user' => $user]);
		foreach($results as $result){
			$repo->remove($result,true);
		}
	}
	
	/**
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 */
	public function beforeScenario(BeforeScenarioScope $scope)
	{
		$this->select2Context = $scope->getEnvironment()->getContext(Select2Context::class);
		if(is_null(static::$jobRepo)){
			$this->gatherContexts($scope);
			static::$jobRepo = $this->getJobRepository();
		}
	}
	
	/**
	 * @Given I go to job board page
	 */
	public function iGoToJobBoardPage()
	{
		$this->visit('/jobboard');
	}
	
	/**
	 * @Given I go to create job page
	 */
	public function iGoToCreateJob()
	{
	    $url = $this->buildUrl('lang/jobs/manage',['action' => 'edit']);
		$this->visit($url);
	}
	
	/**
	 * @Given I go to job overview page
	 */
	public function iGoToJobOverviewPage()
	{
		$this->visit('/jobs');
	}
	
	/**
	 * @Given I go to edit job draft with title :jobTitle
	 * @param $jobTitle
	 * @throws \Exception when job is not found
	 */
	public function iGoToEditJobWithTitle($jobTitle)
	{
		$job = $this->getJobRepository()->findOneBy(['title' => $jobTitle]);
		if(!$job instanceof Job){
			throw new \Exception(sprintf('Job with title "%s" is not found',$jobTitle));
		}
		$this->currentJob = $job;
		$url = $this->buildUrl('lang/jobs/manage',[
		    'id' => $job->getId()
        ]);
		$this->visit($url);
	}
	
	/**
	 * @Given I don't have any classification data
	 */
	public function iDonTHaveAnyClassificationData()
	{
		$this->currentJob->setClassifications(new Classifications());
		$this->getJobRepository()->store($this->currentJob);
	}
	
	/**
	 * @When I don't have any posted job
	 */
	public function iDonTHaveAnyPostedJob()
	{
		/* @var $jobRepository JobRepository */
		/* @var $job Job */
		$user = $this->getUserContext()->getCurrentUser();

		$jobRepository = $this->getJobRepository();
		$results = $jobRepository->getUserJobs($user->getId());
		foreach($results as $job){
			$jobRepository->remove($job,true);
		}
		$this->currentJob = null;
	}
	
	/**
	 * @When I fill job location search with :search and choose :choice
	 *
	 */
	public function iFillJobLocationAndChoose($search,$choice)
	{
		$select2 = $this->select2Context;
		$select2->iFillInSelect2FieldWith('jobBase[geoLocation]',$search,$choice);
	}
	
	/**
	 * @When I choose :value from :field
	 */
	public function iJobClassificationSelect($value,$field)
	{
		$field = Inflector::camelize($field);
		
		$mapSelect2 = [
			'professions' => '#classifications-professions-span .select2-container',
			'industries'  => '#classifications-industries-span .select2-container',
			'employmentTypes' => '#classifications-employmentTypes-span .select2-container',
		];
		
		$mapMultiple = [
			'professions'       => "select#classifications-professions",
			'industries'        => "select#classifications-industries",
			'employmentTypes'    => "select#classifications-employmentTypes",
		];
		
		if(!isset($mapSelect2[$field])){
			throw new \Exception('Undefined field selection value "'.$field.'"');
		}
		
		$multipleField = $mapMultiple[$field];
		$page = $this->minkContext->getSession()->getPage();
		$element = $page->find('css',$mapMultiple[$field]);
		if(!is_null($element) && $element->getAttribute('multiple')=='multiple'){
			$this->minkContext->selectOption($value,$multipleField);
		}else{
			$locator = $mapSelect2[$field];
			$this->select2Context->iFillInSelect2Field($locator,$value);
		}
	}
	
	/**
	 * @return JobRepository
	 */
	public function getJobRepository()
	{
		return $this->getRepository('Jobs/Job');
	}
	
	/**
	 * @return CategoriesRepo
	 */
	public function getCategoriesRepository()
	{
		return $this->getRepository('Jobs/Category');
	}
	
	/**
	 * @When I have a :status job with the following:
	 * @param TableNode $fields
	 */
	public function iHaveAJobWithTheFollowing($status,TableNode $fields)
	{
		$this->buildJob($status,$fields->getRowsHash());
	}

	public function buildJob($status, $definitions,$organization = null)
    {
        $normalizedField = [
            'template' => 'modern',
        ];
        foreach($definitions as $field => $value){
            $field = Inflector::camelize($field);
            if($field == 'professions' || $field == 'industries'){
                $value = explode(',',$value);
            }
            $normalizedField[$field] = $value;
        }

        $jobRepo = $this->getJobRepository();
        $job = $jobRepo->findOneBy([
            'title' => $normalizedField['title']
        ]);
        if(!$job instanceof Job){
            $job = new Job();
            $job->setTitle($normalizedField['title']);
        }

        if(isset($normalizedField['user'])){
            /* @var $userRepo UserRepository */
            $userRepo = $this->getRepository('Auth\Entity\User');
            $user = $userRepo->findOneBy(['login' => $normalizedField['user']]);
            if(is_null($organization)){
                $organization = $user->getOrganization()->getOrganization();
            }
            if($user instanceof User){
                $job->setUser($user);
                $job->setOrganization($organization);
            }else{
                throw new \Exception('There is no user with this login:"'.$normalizedField['user'.'"']);
            }
        }

        if($status == 'draft'){
            $job->setIsDraft(true);
        }elseif($status == 'published'){
            $job->setIsDraft(false);
            $job->setDatePublishStart(new \DateTime());
        }
        $job->setStatus(Status::ACTIVE);

        if(isset($normalizedField['location'])){
            $this->setLocation($job,$normalizedField['location']);
        }
        if(isset($normalizedField['professions'])){
            $this->addProfessions($job,$normalizedField['professions']);
        }

        if(isset($normalizedField['industries'])){
            $this->addIndustries($job,$normalizedField['industries']);
        }
        if(isset($normalizedField['employmentTypes'])){
            $types = $this->getCategories([$normalizedField['employmentTypes']]);
            $type = array_shift($types);
            $values = $job->getClassifications()->getEmploymentTypes()->getValues();
            if(!is_array($values) || !in_array($type,$values)){
                $job->getClassifications()->getEmploymentTypes()->getItems()->add($type);
            }
        }

        $jobRepo->store($job);
        $this->currentJob = $job;
    }

	
	private function setLocation(Job $job, $term)
	{
		/* @var $client Photon */
		$client = $this->coreContext->getServiceManager()->get('Geo/Client');
		$result = $client->queryOne($term);
		$location = new Location();
		$serialized = Json::encode($result);
		$location->fromString($serialized);
		
		$locations = $job->getLocations();
		if(count($locations)){
			$locations->clear();
		}
		$job->getLocations()->add($location);
	}
	
	private function addProfessions(Job &$job,$terms)
	{
		$professions = $this->getCategories($terms);
		foreach($professions as $profession){
			$values = $job->getClassifications()->getProfessions()->getValues();
			if(!is_array($values) || !in_array($profession,$values)){
				$job->getClassifications()->getProfessions()->getItems()->add($profession);
			}
		}
	}
	
	private function addIndustries(Job &$job, $terms)
	{
		$industries = $this->getCategories($terms);
		foreach($industries as $industry){
			$values = $job->getClassifications()->getIndustries()->getValues();
			if(!is_array($values) || !in_array($industry,$values)){
				$job->getClassifications()->getIndustries()->getItems()->add($industry);
			}
		}
	}
	
	/**
	 * @param array $categories
	 *
	 * @return mixed
	 */
	private function getCategories(array $categories)
	{
		$catRepo = $this->getCategoriesRepository();
		
		// get a professions
		$qb = $catRepo->createQueryBuilder()
		              ->field('name')->in($categories)
		              ->getQuery()
		;
		$results = $qb->execute();
		return $results->toArray();
	}
	
	
	/**
	 * @return Job
	 */
	private function getCurrentUserJobDraft($jobTitle)
	{
		$repo = $this->getJobRepository();
		$user = $this->getCurrentUser();
		
		$job = $repo->findDraft($user);
		
		if(is_null($job)){
			$job = new Job();
			$job
				->setUser($user)
				->setOrganization($user->getOrganization()->getOrganization())
				->setStatus(StatusInterface::CREATED)
			;
			$job->setIsDraft(true);
		}
		$job->setTitle($jobTitle);
		$repo->store($job);
		return $job;
	}
}

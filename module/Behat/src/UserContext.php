<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Yawik\Behat;

use Auth\Entity\User as User;
use Auth\Entity\UserInterface;
use Auth\Repository\User as UserRepository;
use Auth\Service\Register;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Core\Entity\Permissions;
use Doctrine\Common\Util\Inflector;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Organizations\Repository\Organization as OrganizationRepository;
use Yawik\Behat\Exception\FailedExpectationException;

class UserContext implements Context
{
    use CommonContextTrait;
	
	/**
	 * @var User[]
	 */
	static private $users = [];
	
	/**
	 * @var UserRepository
	 */
	static private $userRepo;
	
	private $socialLoginInfo = [];

    /**
     * @var DocumentManager
     */
	static private $dm;
	
	/**
	 * @var UserInterface
	 */
	private $loggedInUser;

    /**
     * @var Organization
     */
	private $mainOrganization;

    /**
     * @var User
     */
    protected $currentUser;
	
	public function __construct($parameters=[])
	{
		$defaultLoginInfo = [
			'facebook' => [
				'email' => getenv('FACEBOOK_USER_EMAIL'),
				'pass' => getenv('FACEBOOK_USER_PASSWORD')
			],
			'linkedin' => [
				'session_key-login' => getenv('LINKEDIN_USER_EMAIL'),
				'session_password-login' => getenv('LINKEDIN_USER_PASSWORD')
			],
		];
		$socialLoginConfig = isset($parameters['social_login_info']) ? $parameters['social_login_info']:[];
		$this->socialLoginInfo = array_merge($defaultLoginInfo,$socialLoginConfig);
	}

    /**
     * Empty all data every each tests
     *
     * @AfterSuite
     */
	static public function tearDown()
    {
        $dm = static::$dm;

        $documents = [
            'Applications\Entity\Application',
            'Cv\Entity\Cv',
            'Jobs\Entity\Job',
            'Organizations\Entity\Organization',
            'Auth\Entity\User',
            'Jobs\Entity\Category',
            'Auth\Entity\UserImage',
            'Organizations\Entity\OrganizationName',
        ];
        foreach($documents as $document){
            $dm->createQueryBuilder($document)
                ->remove()
                ->getQuery()
                ->execute()
            ;
        }
    }

	/**
	 * @BeforeScenario
	 * @param BeforeScenarioScope $scope
	 */
	public function beforeScenario(BeforeScenarioScope $scope)
	{
		$this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
		$this->coreContext = $scope->getEnvironment()->getContext(CoreContext::class);
		static::$userRepo = $this->getUserRepository();
		static::$dm = $this->getUserRepository()->getDocumentManager();
	}
	
	/**
	 * @When I fill in login form with :provider user
	 */
	public function iSignInWithSocialUser($provider)
	{
		$provider = strtolower($provider);
		$mink = $this->minkContext;
		foreach($this->socialLoginInfo[$provider] as $field=>$value){
			$mink->fillField($field,$value);
		}
	}
	
	/**
	 * @Given I am logged in as a recruiter
	 * @Given I am logged in as a recruiter with :organization as organization
	 */
	public function iAmLoggedInAsARecruiter($organization=null)
	{
		$user = $this->thereIsAUserIdentifiedBy(
			'test@recruiter.com',
			'test',User::ROLE_RECRUITER,
			'Test Recruiter',
			$organization
		);
		$this->startLogin($user,'test');
		if(!is_null($organization)){
            $this->iHaveMainOrganization($user,$organization);
        }
	}

    /**
     * @Given I am logged out
     */
    public function iHaveLoggedOut()
    {
        $url = $this->minkContext->locatePath('/logout');
        $this->minkContext->getSession()->visit($url);
    }

	
	/**
	 * @Given I don't have :login user
	 * @param string $login
	 */
	public function iDonTHaveUser($login)
	{
		$repo = $this->getUserRepository();
		$user=$repo->findByLogin($login);
		if($user instanceof UserInterface){
			$repo->remove($user,true);
		}
	}
	
	/**
	 * @Given I have a :role with the following:
     * @Given I have an :role with the following:
     *
	 * @param $role
	 * @param TableNode $fields
	 */
	public function iHaveUserWithTheFollowing($role,TableNode $fields)
	{
		$normalizedFields = [
			'login' => 'test@login.com',
			'fullName' => 'Test Login',
			'role' => $role,
			'password' => 'test',
			'organization' => 'Cross Solution'
		];
		foreach($fields->getRowsHash() as $field=>$value){
			$field = Inflector::camelize($field);
			$normalizedFields[$field] = $value;
		}
		
		$this->thereIsAUserIdentifiedBy(
			$normalizedFields['login'],
			$normalizedFields['password'],
			$role,
			$normalizedFields['fullName'],
			$normalizedFields['organization']
		);
		
	}
	
	/**
	 * @Given I am logged in as an administrator
	 */
	public function iAmLoggedInAsAnAdmin()
	{
		$user = $this->thereIsAUserIdentifiedBy('test@admin.com','test',User::ROLE_ADMIN);
		$this->startLogin($user,'test');
	}
	
	private function startLogin(UserInterface $user, $password)
	{
		$currentUser = $this->currentUser;
		if(!is_object($currentUser) || $user->getId()!=$currentUser->getId()){
			$this->iWantToLogIn();
			$this->iSpecifyTheUsernameAs($user->getLogin());
			$this->iSpecifyThePasswordAs($password);
			$this->iLogIn();
			$this->currentUser = $user;
		}
	}
	
	/**
	 * @return UserRepository
	 */
	public function getUserRepository()
	{
		return $this->coreContext->getRepositories()->get('Auth\Entity\User');
	}
	
	/**
	 * @Given there is a user :email identified by :password
	 */
	public function thereIsAUserIdentifiedBy($email, $password,$role=User::ROLE_RECRUITER,$fullname="Test Recruiter",$organization=null)
	{
		$repo = $this->getUserRepository();
		if(!is_object($user=$repo->findByEmail($email))){
			$user = $this->createUser($email,$password,$role,$fullname,$organization);
		}
		
		if(!is_null($organization)){
			$this->iHaveMainOrganization($user,$organization);
		}
		$this->addCreatedUser($user);
		$repo->getDocumentManager()->refresh($user);
		return $user;
	}
	
	/**
	 * @param $email
	 * @param $password
	 * @param $username
	 * @param string $fullname
	 * @param string $role
	 *
	 * @return \Auth\Entity\UserInterface
	 */
	public function createUser($email,$password,$role=User::ROLE_RECRUITER,$fullname="Test Recruiter")
	{
		/* @var Register $service */
		/* @var User $user */
		$repo = $this->getUserRepository();
		$user = $repo->create([]);
		$user->setLogin($email);
		$user->setPassword($password);
		$user->setRole($role);
		$settings = $user->getSettings('Applications');
		
		$expFullName = explode(' ',$fullname);
		$info = $user->getInfo();
		$info->setFirstName(array_shift($expFullName));
		$info->setLastName(count($expFullName)>0 ? implode(' ',$expFullName):'');
		$info->setEmail($email);
		$info->setEmailVerified(true);
		$repo->store($user);
		$repo->getDocumentManager()->refresh($user);
		
		$eventArgs = new LifecycleEventArgs($user, $repo->getDocumentManager());
		$repo->getDocumentManager()->getEventManager()->dispatchEvent(
			Events::postLoad,
			$eventArgs
		);
		/* @var \Core\EventManager\EventManager $events */
		/* @var \Auth\Listener\Events\AuthEvent $event */
		//@TODO: [Behat] event not working in travis
		//$events = $this->coreContext->getEventManager();
		//$event  = $events->getEvent(AuthEvent::EVENT_USER_REGISTERED, $this);
		//$event->setUser($user);
		//$events->triggerEvent($event);
		return $user;
	}
	
	/**
	 * @When I have :organization as my main organization
	 * @param $orgName
	 */
	public function iHaveMainOrganization(UserInterface $user,$orgName)
	{
		/* @var $repoOrganization OrganizationRepository */
		$repoOrganization = $this->coreContext->getRepositories()->get('Organizations/Organization');
		$result = $repoOrganization->findByName($orgName);
		$organization = count($result) > 0 ? $result[0]:null;
		if(!$organization instanceof Organization){
			$organization = new Organization();
			$organizationName = new OrganizationName($orgName);
			$organization->setOrganizationName($organizationName);
		}
        $organization->setProfileSetting(Organization::PROFILE_ALWAYS_ENABLE);
        $permissions = $organization->getPermissions();
        $permissions->grant($user,Permissions::PERMISSION_ALL);

        $organization->setUser($user);
        $repoOrganization->store($organization);
        $repoOrganization->getDocumentManager()->refresh($organization);

        $this->mainOrganization = $organization;
	}

    /**
     * @return Organization
     */
    public function getMainOrganization()
    {
        return $this->mainOrganization;
    }

	/**
	 * @When I want to log in
	 */
	public function iWantToLogIn()
	{
		$session = $this->minkContext->getSession();
		$url = $this->buildUrl('lang/auth');
		$session->visit($url);
	}
	
	/**
	 * @When I specify the username as :username
	 */
	public function iSpecifyTheUsernameAs($username)
	{
		$this->minkContext->fillField('Login name',$username);
	}
	
	/**
	 * @When I specify the password as :password
	 */
	public function iSpecifyThePasswordAs($password)
	{
		$this->minkContext->fillField('Password',$password);
	}
	
	/**
	 * @Given I am logged in as :username identified by :password
	 */
	public function iAmLoggedInAsIdentifiedBy($username, $password)
	{
		$repo = $this->getUserRepository();
		$user = $repo->findByLogin($username);
		
		if(!$user instanceof User){
			throw new \Exception(sprintf('There is no user with this login: "%s"',$username));
		}
		$this->iWantToLogIn();
		$this->iSpecifyTheUsernameAs($username);
		$this->iSpecifyThePasswordAs($password);
		$this->iLogIn();
        $this->currentUser = $user;
	}
	
	/**
	 * @When I log in
	 */
	public function iLogIn()
	{
		$this->minkContext->pressButton('login');
	}
	
	/**
	 * @When I press logout link
	 */
	public function iPressLogoutLink()
	{
		$url = $this->buildUrl('auth-logout');
		$this->visit($url);
	}
	
	/**
	 * @Given I log in with username :username and password :password
	 */
	public function iLogInWith($username, $password)
	{
		$repo = $this->getUserRepository();
		$user = $repo->findByLogin($username);
		$this->iWantToLogIn();
		$this->iSpecifyTheUsernameAs($username);
		$this->iSpecifyThePasswordAs($password);
		$this->iLogIn();
		$this->loggedInUser = $user;
	}
	
	/**
	 * @When I go to profile page
	 */
	public function iGoToProfilePage()
	{
		$url = $this->buildUrl('lang/my');
		$this->visit($url);
	}
	
	/**
	 * @Given there is a user with the following:
	 */
	public function thereIsAUserWithTheFollowing(TableNode $table)
	{
		$repo = $this->getUserRepository();
		$data = $table->getRowsHash();
		$email = isset($data['email']) ? $data['email']:'test@example.com';
		$password = isset($data['password']) ? $data['password']:'test';
		$fullname = isset($data['fullname']) ? $data['fullname']:'Test User';
		$role = isset($data['role']) ? $data['role']:User::ROLE_RECRUITER;
		
		if(!is_object($user=$repo->findByLogin($email))){
			$user = $this->createUser($email,$password,$role,$fullname);
		}
		$this->currentUser = $user;
		$this->addCreatedUser($user);
	}
	
	private function addCreatedUser(UserInterface $user)
	{
		if(!in_array($user,static::$users)){
			static::$users[] = $user;
		}
	}
	
	/**
	 * @When I want to change my password
	 */
	public function iWantToChangeMyPassword()
	{
		$url = $this->buildUrl('lang/my-password');
		$this->visit($url);
	}

    /**
     * @return User
     * @throws FailedExpectationException
     */
	public function getCurrentUser()
    {
        if(!$this->currentUser instanceof User){
            throw new FailedExpectationException('Need to login first before use this step');
        }
        return $this->currentUser;
    }
}


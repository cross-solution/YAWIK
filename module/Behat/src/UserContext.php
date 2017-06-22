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
use Auth\Listener\Events\AuthEvent;
use Auth\Repository\User as UserRepository;
use Auth\Service\Register;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

class UserContext implements Context
{
	/**
	 * @var CoreContext
	 */
	private $coreContext;
	
	/**
	 * @var MinkContext
	 */
	private $minkContext;
	
	/**
	 * @BeforeScenario
	 * @param BeforeScenarioScope $scope
	 */
	public function gatherContexts(BeforeScenarioScope $scope)
	{
		$this->coreContext = $scope->getEnvironment()->getContext(CoreContext::class);
		$this->minkContext = $scope->getEnvironment()->getContext(MinkContext::class);
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
	public function thereIsAUserIdentifiedBy($email, $password,$name='test.user')
	{
		$repo = $this->getUserRepository();
		if(!is_object($user=$repo->findByEmail($email))){
			$this->createUser($email,$password,$name);
		}else{
			$user->setPassword($password);
			$user->setLogin($name);
			$repo->store($user);
		}
	}
	
	public function createUser($email,$password,$name="Test Recruiter",$role=User::ROLE_RECRUITER)
	{
		/* @var Register $service */
		$repo = $this->getUserRepository();
		$user = $repo->create([
			'login' => $email,
			'name' => $name,
			'password' => $password,
			'role' => $role
		]);
		$info = $user->getInfo();
		$info->setEmail($email);
		$info->setEmailVerified(true);
		
		$user->setPassword(uniqid('credentials', true));
		$repo->store($user);
		
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
	 * @When I want to log in
	 */
	public function iWantToLogIn()
	{
		$session = $this->minkContext->getSession();
		$url = $this->minkContext->locatePath('/en/login');
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
		$this->iWantToLogIn();
		$this->iSpecifyTheUsernameAs($username);
		$this->iSpecifyThePasswordAs($password);
		$this->iLogIn();
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
		//@TODO: [ZF3] replace this with click method
		$url = $this->coreContext->generateUrl('/logout');
		$this->minkContext->visit($url);
	}
	
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Auth\AuthenticationService;
use Auth\Service\Exception;
use Auth\Options\ModuleOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Log\LoggerInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;

/**
 * Main Action Controller for Authentication module.
 */
class IndexController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var
     */
    protected $loginForm;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @param $auth  AuthenticationService
     * @param $logger LoggerInterface
     * @param $loginForm
     * @param $options
     */
    public function __construct(AuthenticationService $auth, LoggerInterface $logger, $loginForm, $options)
    {
        $this->auth=$auth;
        $this->loginForm=$loginForm;
        $this->logger=$logger;
        $this->options = $options;
    }

    /**
     * Login with username and password
     */
    public function indexAction()
    {
        if ($this->auth->hasIdentity()) {
            return $this->redirect()->toRoute('lang');
        }

        $viewModel = new ViewModel();
        $services  = $this->getServiceLocator();
        $form      = $this->loginForm;
        
        if ($this->request->isPost()) {
            $data                          = $this->params()->fromPost();
            $adapter                       = $services->get('Auth/Adapter/UserLogin');
            // inject suffixes via shared Events
            $loginSuffix                   = '';
            // @TODO: replace this by the Plugin LoginFilter
            $e                             = $this->getEvent();
            $loginSuffixResponseCollection = $this->getEventManager()->trigger('login.getSuffix', $e);
            if (!$loginSuffixResponseCollection->isEmpty()) {
                $loginSuffix = $loginSuffixResponseCollection->last();
            }

            $form->setData($data);
            if (array_key_exists('credentials', $data) &&
                array_key_exists('login', $data['credentials']) &&
                array_key_exists('credential', $data['credentials'])) {
                $adapter->setIdentity($data['credentials']['login'] . $loginSuffix)
                    ->setCredential($data['credentials']['credential']);
            }
            
            $auth       = $this->auth;
            $result     = $auth->authenticate($adapter);
            
            
            if ($result->isValid()) {
                $user = $auth->getUser();
                $settings = $user->getSettings('Core');
                $language = $settings->localization->language;
                if (!$language) {
                    $headers = $this->getRequest()->getHeaders();
                    if ($headers->has('Accept-Language')) {
                        $locales = $headers->get('Accept-Language')->getPrioritized();
                        $language  = $locales[0]->type;
                    } else {
                        $language = 'en';
                    }
                }
                $this->logger->info('User ' . $user->login . ' logged in');
                
                $ref = $this->params()->fromQuery('ref', false);

                if ($ref) {
                    $ref = urldecode($ref);
                    $url = preg_replace('~/[a-z]{2}(/|$)~', '/' . $language . '$1', $ref);
                    $url = $this->getRequest()->getBasePath() . $url;
                } else {
                    $urlHelper = $services->get('ViewHelperManager')->get('url');
                    $url = $urlHelper('lang', array('lang' => $language));
                }
                $this->notification()->success(/*@translate*/ 'You are now logged in.');
                return $this->redirect()->toUrl($url);
                
            } else {
                $loginName = $data['credentials']['login'];
                if (!empty($loginSuffix)) {
                    $loginName = $loginName . ' (' . $loginName . $loginSuffix . ')';
                }
                $this->logger->info('Failed to authenticate User ' . $loginName );
                $this->notification()->danger(/*@translate*/ 'Authentication failed.');
            }
        }
        
        $ref = $this->params()->fromQuery('ref', false);
        
        if ($ref) {
            $req = $this->params()->fromQuery('req', false);
            if ($req) {
                $this->getResponse()->setStatusCode(403);
                $viewModel->setVariable('required', true);
            }
            $viewModel->setVariable('ref', $ref);
        }

        $allowRegister = $this->options->getEnableRegistration();
        $allowResetPassword = $this->options->getEnableResetPassword();
        if (isset($allowRegister)) {
            $viewModel->setVariables(
                [
                    'allowRegister' => $allowRegister,
                    'allowResetPassword' => $allowResetPassword
                ]
            );
        }
        
        $viewModel->setVariable('form', $form);
        return $viewModel;
    }
    
    /**
     * Login with HybridAuth
     *
     * Passed in Params:
     * - provider: HybridAuth provider identifier.
     *
     * Redirects To: Route 'home'
     */
    public function loginAction()
    {
        $ref = urldecode($this->getRequest()->getBasePath().$this->params()->fromQuery('ref'));
        $provider = $this->params('provider', '--keiner--');
        $hauth = $this->getServiceLocator()->get('HybridAuthAdapter');
        $hauth->setProvider($provider);
        $auth = $this->auth;
        $result = $auth->authenticate($hauth);
        $resultMessage = $result->getMessages();

        if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === true) {
            try {
                $user          = $auth->getUser();
                $password      = substr(md5(uniqid()), 0, 6);
                $login         = uniqid() . ($this->options->auth_suffix != "" ? '@' . $this->options->auth_suffix : '');
                $externalLogin = isset($user->login)?$user->login:'-- not communicated --';
                $this->logger->debug('first login via ' . $provider . ' as: ' . $externalLogin);

                $user->login=$login;
                $user->setPassword($password);
                $user->role = $this->options->getRole();

                $mail = $this->mailer('htmltemplate');
                $mail->setTemplate('mail/first-socialmedia-login');
                $mail->setSubject($this->options->getMailSubjectRegistration());
                $mail->setVariables(
                    array(
                                'displayName'=> $user->getInfo()->getDisplayName(),
                                'provider' => $provider,
                                'login' => $login,
                                'password' => $password,
                    )
                );
                $mail->addTo($user->getInfo()->getEmail());

                $loggerId = $login . ' (' . $provider . ': ' . $externalLogin . ')';
                if (isset($mail) && $this->mailer($mail)) {
                    $this->logger->info('Mail first-login for ' . $loggerId . ' sent to ' . $user->getInfo()->getEmail());
                } else {
                    $this->logger->warn('No Mail was sent for ' . $loggerId);
                }
            } catch (\Exception $e) {
                $this->logger->crit($e);
                $this->notification()->danger(
                    /*@translate*/ 'An unexpected error has occurred, please contact your system administrator'
                );
            }
        }
        
        $user = $auth->getUser();
        $this->logger->info('User ' . $auth->getUser()->getInfo()->getDisplayName() . ' logged in via ' . $provider);
        $settings = $user->getSettings('Core');
        if (null !== $settings->localization->language) {
            $basePath = $this->getRequest()->getBasePath();
            $ref = preg_replace('~^'.$basePath . '/[a-z]{2}(/)?~', $basePath . '/' . $settings->localization->language . '$1', $ref);
        }
        return $this->redirect()->toUrl($ref);
    }
    
    /**
     * Login via an external Application. This will get obsolet as soon we'll have a full featured Rest API.
     *
     * Passed in params:
     * - appKey: Application identifier key
     * - user: Name of the user to log in
     * - pass: Password of the user to log in
     *
     * Returns an json response with the session-id.
     * Non existant users will be created!
     *
     */
    public function loginExternAction()
    {
        $services   = $this->getServiceLocator();
        $adapter    = $services->get('ExternalApplicationAdapter');
        $appKey     = $this->params()->fromPost('appKey');

        $adapter->setIdentity($this->params()->fromPost('user'))
                ->setCredential($this->params()->fromPost('pass'))
                ->setApplicationKey($appKey);
        
        $auth       = $this->auth;
        $result     = $auth->authenticate($adapter);
        
        if ($result->isValid()) {
            $this->logger->info(
                'User ' . $this->params()->fromPost('user') .
                ' logged via ' . $appKey
            );
            
            // the external login may include some parameters for an update
            $updateParams = $this->params()->fromPost();
            unset($updateParams['user'], $updateParams['pass'], $updateParams['appKey']);
            $resultMessage = $result->getMessages();
            $password = null;
            if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === true) {
                $password = substr(md5(uniqid()), 0, 6);
                $updateParams['password'] = $password;
            }
            if (!empty($updateParams)) {
                $user = $auth->getUser();
                try {
                    foreach ($updateParams as $updateKey => $updateValue) {
                        if ('email' == $updateKey) {
                            $user->info->email = $updateValue;
                        }
                        $user->$updateKey = $updateValue;
                    }
                } catch (Exception $e) {
                }
                $services->get('repositories')->store($user);
            }
            
            $resultMessage = $result->getMessages();
            // TODO: send a mail also when required (maybe first mail failed or email has changed)
            if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === true) {
                // first external Login
                $userName = $this->params()->fromPost('user');
                $this->logger->debug('first login for User: ' .  $userName);
                //
                if (preg_match("/^(.*)@\w+$/", $userName, $realUserName)) {
                    $userName = $realUserName[1];
                }

                $mail = $this->mailer('htmltemplate'); /* @var $mail \Core\Mail\HTMLTemplateMessage */
                $apps = $this->config('external_applications');
                $apps = array_flip($apps);
                $application = isset($apps[$appKey]) ? $apps[$appKey] : null;

                $mail->setVariables(
                    array(
                    'application' => $application,
                    'login'=>$userName,
                    'password' => $password,
                    )
                );
                $mail->setSubject($this->options->getMailSubjectRegistration());
                $mail->setTemplate('mail/first-external-login');
                $mail->addTo($user->getInfo()->getEmail());

                try {
                    $this->mailer($mail);
                    $this->logger->info('Mail first-login sent to ' . $userName);
                } catch (\Zend\Mail\Transport\Exception\ExceptionInterface $e) {
                    $this->logger->warn('No Mail was sent');
                    $this->logger->debug($e);
                }
            }

            return new JsonModel(
                array(
                'status' => 'success',
                'token' => session_id()
                )
            );
        } else {
            $this->logger->info(
                'Failed to authenticate User ' . $this->params()->fromPost('user') .
                ' via ' . $this->params()->fromPost('appKey')
            );
            
            $this->getResponse()->setStatusCode(403);
            return new JsonModel(
                array(
                'status' => 'failure',
                'user' => $this->params()->fromPost('user'),
                'appKey' => $this->params()->fromPost('appKey'),
                'code'   => $result->getCode(),
                'messages' => $result->getMessages(),
                )
            );
        }
        
    }
    
    public function groupAction()
    {
        //$adapter = $this->getServiceLocator()->get('ExternalApplicationAdapter');
        if (false) {
             $this->request->setMethod('get');
            $params = new Parameters(
                array(
                'format' => 'json',
                    'group' => array (
                        0 => 'testuser4711', 1 => 'flatscreen', 2 => 'flatscreen1', 3 => 'flatscreen2', 4 => 'flatscreen3',  5 => 'flatscreen4',
                        6 => 'flatscreen5', 7 => 'flatscreen6', 8 => 'flatscreen7',  9 => 'flatscreen8', 10 => 'flatscreen9'
                    ),
                    'name' => '(die) Rauscher – Unternehmensberatung & Consulting',
                )
            );
            $this->getRequest()->setQuery($params);
             
        }
        $auth = $this->auth;
        $userGrpAdmin = $auth->getUser();
        $this->logger->info('User ' . $auth->getUser()->getInfo()->getDisplayName());
        $grp = $this->params()->fromQuery('group');
        
        // if the request is made by an external host, add his identification-key to the name
        $loginSuffix = '';
        // @TODO: replace this by the Plugin LoginFilter
        $e = $this->getEvent();
        $loginSuffixResponseCollection = $this->getEventManager()->trigger('login.getSuffix', $e);
        if (!$loginSuffixResponseCollection->isEmpty()) {
            $loginSuffix = $loginSuffixResponseCollection->last();
        }
        // make out of the names a list of Ids
        $params = $this->getRequest()->getQuery();
        $groupUserId = array();
        $notFoundUsers = array();
        //$users = $this->getRepository();
        $users = $this->getServiceLocator()->get('repositories')->get('Auth/User');
        if (!empty($params->group)) {
            foreach ($params->group as $grp_member) {
                $user = $users->findByLogin($grp_member . $loginSuffix);
                if (!empty($user)) {
                    $groupUserId[] = $user->id;
                } else {
                    $notFoundUsers[] = $grp_member . $loginSuffix;
                }
            }
        }
        $name = $params->name;
        if (!empty($params->name)) {
            $group = $this->auth()->getUser()->getGroup($params->name, /*create*/ true);
            $group->setUsers($groupUserId);
        }
        $this->logger->info(
            'Update Group Name: ' . $name . PHP_EOL . str_repeat(' ', 36) . 'Group Owner: ' . $userGrpAdmin->getLogin() . PHP_EOL .
            str_repeat(' ', 36) . 'Group Members Param: ' . implode(',', $params->group) . PHP_EOL .
            str_repeat(' ', 36) . 'Group Members: ' . count($groupUserId) . PHP_EOL . str_repeat(' ', 36) . 'Group Members not found: ' . implode(',', $notFoundUsers)
        );
        
        return new JsonModel(
            array(
            )
        );
    }
    
    /**
     * Logout
     *
     * Redirects To: Route 'home'
     */
    public function logoutAction()
    {
        $auth = $this->auth;
        $this->logger->info('User ' . ($auth->getUser()->login==''?$auth->getUser()->info->displayName:$auth->getUser()->login) . ' logged out');
        $auth->clearIdentity();
        unset($_SESSION['HA::STORE']);

        $this->notification()->success(/*@translate*/ 'You are now logged out');
        return $this->redirect()->toRoute(
            'lang',
            array('lang' => $this->params('lang'))
        );
    }
}

<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;

//@codeCoverageIgnoreStart 

/**
 * Main Action Controller for Authentication module.
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Login with username and password
     */
    public function indexAction()
    { 
        
        $viewModel = new ViewModel();
        $services = $this->getServiceLocator();
        $form     = $services->get('FormElementManager')
                             ->get('user-login');
        
        
        if ($this->request->isPost()) {
            
            $form->setData($this->params()->fromPost());
            $adapter     = $services->get('auth-login-adapter');
            
            // inject suffixes via shared Events
            $loginSuffix = '';
            $e = $this->getEvent();
            //$this->getEventManager()->addIdentifiers('login');
            $loginSuffixResponseCollection = $this->getEventManager()->trigger('login.getSuffix', $e);
            if (!$loginSuffixResponseCollection->isEmpty()) {
                $loginSuffix = $loginSuffixResponseCollection->last();
            }
            
            $data = $this->params()->fromPost();
            $adapter->setIdentity($data['credentials']['login'] . $loginSuffix)
                    ->setCredential($data['credentials']['credential']);
            
            
            $auth       = $services->get('AuthenticationService');
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
                $services->get('Log')->info('User ' . $user->login . ' logged in');
                
                $ref = $this->params()->fromQuery('ref', false);

                if ($ref) {
                    $ref = urldecode($ref);
                    $url = preg_replace('~^/[a-z]{2}(/)?~', '/' . $language . '$1', $ref);
                    $url = $this->getRequest()->getBasePath() . $url;
                } else {
                    $urlHelper = $services->get('ViewHelperManager')->get('url');
                    $url = $urlHelper('lang', array('lang' => $language));
                }
                if ($this->request->isXmlHttpRequest()) {
                    
                    return new JsonModel(array(
                        'ok' => true,
                        'redirect' => $url,
                    ));
                }
                return $this->redirect()->toUrl($url);
                
            } else {
                
                $services->get('Log')->info('Failed to authenticate User ' . $data['credentials']['login'] );
                
                $translator = $services->get('translator');
                $vars = array(
                    'ok' => false,
                    'status' => 'error',
                    'text' => $translator->translate('Authentication failed.')
                );
                if ($this->request->isXmlHttpRequest()) {
                    return new JsonModel($vars);
                }
                $viewModel->setVariables($vars);
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
        
        $viewModel->setVariable('form', $form);
        return $viewModel;
        //var_dump($this->getServiceLocator()->get('Config'));
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
        
        $ref = urldecode($this->params()->fromQuery('ref'));
        $provider = $this->params('provider', '--keiner--');
        $hauth = $this->getServiceLocator()->get('HybridAuthAdapter');
        $hauth->setProvider($provider);
        $auth = $this->getServiceLocator()->get('AuthenticationService');
        $result = $auth->authenticate($hauth);
        $resultMessage = $result->getMessages();
        if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === True) {
            // erstes Login
            $this->getServiceLocator()->get('Log')->debug('first login via ' . $provider);
            
            if (array_key_exists('user', $resultMessage)) {
                $user = $resultMessage['user'];
                $lastName = $user->info->getDisplayName();
                // TODO
                $mail = $this->mail(
                        array('Anrede'=>$lastName,
                             'password' => '***',
                             'domain' => '***')
                        );
                $mail->template('first-login');
                $mail->addTo($user->info->getEmail());
                $mail->setFrom('cross@cross-solution.de', 'Cross Applicant Management');
                $mail->setSubject('Anmeldung im Cross Applicant Management');
            }
            if (isset($mail) && $mail->send()) {
                $this->getServiceLocator()->get('Log')->info('Mail first-login sent to ' . $user->info->getEmail());
            } else {
                $this->getServiceLocator()->get('Log')->warn('No Mail was sent');
            }
            
        }
        
        $user = $auth->getUser();
        $this->getServiceLocator()->get('Log')->info('User ' . $auth->getUser()->getInfo()->getDisplayName() . ' logged in via ' . $provider);
        $settings = $user->settings;
        if (isset($settings['settings']['language'])) {
            $ref = preg_replace('~^/[a-z]{2}(/)?~', '/' . $settings['settings']['language'] . '$1', $ref);
        } 
        $this->redirect()->toUrl($ref); //Route('lang/home', array('lang' => $this->params('lang')));
    }
    
    /**
     * Login via an external Application.
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
       
//         if (!$this->getRequest()->isPost()) {
//             return new JsonModel(array(
//                 'status' => 'failure',
//                 'code'   => 1,
//                 'messages' => 'Authentification requires a post request.',
//             ));
//         }
        
         if (False) {
            // Test
            $this->request->setMethod('post');
            $params = new Parameters(array(
                'user' => 'dummy_' . uniqid() . '@ams',
                'pass' => 'passwordfromams1',
                'appKey' => 'AmsAppKey',
                'email' => 'weitz@cross-solution.de',
            ));
            $this->getRequest()->setPost($params);
        }
        
        $services   = $this->getServiceLocator();
        $adapter    = $services->get('ExternalApplicationAdapter');
        $config     = $services->get('config');

        $adapter->setIdentity($this->params()->fromPost('user'))
                ->setCredential($this->params()->fromPost('pass'))
                ->setApplicationKey($this->params()->fromPost('appKey'));
        
        $auth       = $services->get('AuthenticationService');
        $result     = $auth->authenticate($adapter);
        
        if ($result->isValid()) {
            $services->get('Log')->info('User ' . $this->params()->fromPost('user') . 
                                        ' logged via ' . $this->params()->fromPost('appKey'));
            
            // the external login may include some parameters for an update
            $updateParams = $this->params()->fromPost();
            unset ($updateParams['user'], $updateParams['pass'], $updateParams['appKey']);
            $resultMessage = $result->getMessages();
            $password = Null;
            if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === True) {
                $password = substr(uniqid(),0,6);
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
            if (array_key_exists('firstLogin', $resultMessage) && $resultMessage['firstLogin'] === True) {
                // first external Login
                $userName = $this->params()->fromPost('user');
                $this->getServiceLocator()->get('Log')->debug('first login for User: ' .  $userName);
                // 
                if (preg_match("/^(.*)@\w+$/", $userName, $realUserName)) {
                    $userName = $realUserName[1];
                }
                $domain = '';
                $uri = $this->getRequest()->getUri();
                if (isset($uri)) {
                    $domain = $uri->getHost();
                }
                $mail = $this->mail(array(
                    'Anrede'=>$userName, 
                    'password' => $password,
                    'domain' => $domain
                    ));
                $mail->template('first-login');
                $mail->addTo($user->getInfo()->getEmail());
                $mail->informationComplete();
                
                if (isset($mail) && $mail->send()) {
                    $this->getServiceLocator()->get('Log')->info('Mail first-login sent to ' . $userName);
                } else {
                    $this->getServiceLocator()->get('Log')->warn('No Mail was sent');
                }
            }
            
            return new JsonModel(array(
                'status' => 'success',
                'token' => session_id()
            ));
        } else {
            $services->get('Log')->info('Failed to authenticate User ' . $this->params()->fromPost('user') .
                                        ' via ' . $this->params()->fromPost('appKey'));
            
            $this->getResponse()->setStatusCode(403);
            return new JsonModel(array(
                'status' => 'failure',
                'code'   => $result->getCode(),
                'messages' => $result->getMessages(),
            ));
        }
        
    }
    
    /**
     * Logout
     * 
     * Redirects To: Route 'home'
     */
    public function logoutAction()
    {
        $auth = $this->getServiceLocator()->get('AuthenticationService');
        $this->getServiceLocator()->get('Log')->info('User ' . ($auth->getUser()->login==''?$auth->getUser()->info->displayName:$auth->getUser()->login) . ' logged out');
        $auth->clearIdentity();
        unset($_SESSION['HA::STORE']);
        
        $this->redirect()->toRoute(
            'lang', 
            array('lang' => $this->params('lang')),
            array('query' => array('logout' => '1'))
        );
    }
    
}

// @codeCoverageIgnoreEnd 

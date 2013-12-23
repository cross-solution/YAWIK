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

//@codeCoverageIgnoreStart 

/**
 * Main Action Controller for Authentication module.
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Main login site
     *
     */
    public function indexAction()
    { 
        $viewModel = new ViewModel();
        $services = $this->getServiceLocator();
        $form     = $services->get('FormElementManager')
                             ->get('user-login');
        
        
        if ($this->request->isPost()) {
            
            $form->setData($this->params()->fromPost());
            $adapter    = $services->get('auth-login-adapter');
            
            $data = $this->params()->fromPost();
            $adapter->setIdentity($data['credentials']['login'])
                    ->setCredential($data['credentials']['credential']);
            
            
            $auth       = $services->get('AuthenticationService');
            $result     = $auth->authenticate($adapter);
            
            
            if ($result->isValid()) {
                $user = $auth->getUser();
                $settings = $user->settings;
                if ($ref = $this->params()->fromQuery('ref', false)) {
                    if (isset($settings['settings']['language'])) {
                        $ref = preg_replace('~^/[a-z]{2}(/)?~', '/' . $settings['settings']['language'] . '$1', $ref);
                    }
                    $url = $ref;
                } else {
                    $urlHelper = $services->get('ViewHelperManager')->get('url');
                    $url = isset($settings['settings']['language'])
                         ? $urlHelper('lang', array('lang' => $settings['settings']['language']))
                         : $urlHelper('lang', array(), true);
                }
                if ($this->request->isXmlHttpRequest()) {
                    
                
                    return new JsonModel(array(
                        'ok' => true,
                        'redirect' => $url,
                    ));
                }
                return $this->redirect()->toUrl($url);
                
            } else {
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
        
        if ($ref = $this->params()->fromQuery('ref', false)) {
            $this->getResponse()->setStatusCode(403);
            $viewModel->setVariable('ref', $ref)
                      ->setVariable('required', (bool) $this->params()->fromQuery('req', 0));
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
            if (array_key_exists('user', $resultMessage)) {
                $user = $resultMessage['user'];
                $lastName = $user->info->getDisplayName();
                $mail = $this->mail(array('Anrede'=>$lastName));
                $mail->template('first-login');
                $mail->addTo($user->info->getEmail());
                $mail->setFrom('cross@cross-solution.de', 'Cross Applicant Management');
                $mail->setSubject('Anmeldung im Cross Applicant Management');
                $resultMail = $mail->send();
            }
        }
        
        $user = $auth->getUser();
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
        
        $services   = $this->getServiceLocator();
        $adapter    = $services->get('ExternalApplicationAdapter');
        /*
        $adapter->setIdentity('illert')
                ->setCredential('a28d402326a5dda1c349fb849efc720a')
                ->setApplicationKey('AmsAppKey');
        */
        $adapter->setIdentity($this->params()->fromPost('user'))
                ->setCredential($this->params()->fromPost('pass'))
                ->setApplicationKey($this->params()->fromPost('appKey'));
        
        
        $auth       = $services->get('AuthenticationService');
        $result     = $auth->authenticate($adapter);
        
        if ($result->isValid()) {
            return new JsonModel(array(
                'status' => 'success',
                'token' => session_id()
            ));
        } else {
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

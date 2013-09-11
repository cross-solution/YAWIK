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
        $this->getServiceLocator()->get('ExternalApplicationAdapter');
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
        
        $this->redirect()->toRoute('lang/home', array('lang' => $this->params('lang')));
    }
    
}

// @codeCoverageIgnoreEnd 

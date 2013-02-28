<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Main Action Controller for Authentication module.
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Login site
     *
     */
    public function indexAction()
    { 
        
    }
    
    public function loginAction()
    {
        $provider = $this->params('provider', '--keiner--');
        $hauth = $this->getServiceLocator()->get('HybridAuthAdapter');
        $hauth->setProvider($provider);
        $auth = $this->getServiceLocator()->get('AuthenticationService');
        $result = $auth->authenticate($hauth);
        
        
        
        
        $this->redirect()->toRoute('home');
    }
    
}

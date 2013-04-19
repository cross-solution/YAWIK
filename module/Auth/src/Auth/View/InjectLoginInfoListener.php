<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\View;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class InjectLoginInfoListener
{
    
    public function injectLoginInfo(MvcEvent $e)
    {
        if ( ($viewModel = $e->getViewModel()) instanceOf JsonModel) {
            // We don't need the login-info in a json response.
            return;
        }
        

        $loginInfoModel = new ViewModel();
        $loginInfoModel->setTemplate('auth/index/login-info');
        
        $viewModel->addChild($loginInfoModel, 'loginInfo');
    }
}
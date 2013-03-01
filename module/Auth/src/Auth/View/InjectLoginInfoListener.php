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

class InjectLoginInfoListener
{
    
    public function injectLoginInfo(MvcEvent $e)
    {
        $viewModel = $e->getViewModel();

        $loginInfoModel = new ViewModel();
        $loginInfoModel->setTemplate('auth/index/login-info');
        
        $viewModel->addChild($loginInfoModel, 'loginInfo');
    }
}
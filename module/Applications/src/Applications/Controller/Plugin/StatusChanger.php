<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** StatusChanger.php */ 
namespace Applications\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class StatusChanger extends AbstractPlugin
{
    
    public function __invoke()
    {
        return $this;    
    }
    
    
    
    public function mustSendMail()
    {
        $controller = $this->getController();
        $action     = $controller->params('do', 'confirm');
        $isPost     = $controller->getRequest()->isPost();
        
        return in_array($action, array('invite', 'deny')) && !$isPost;
    }
}


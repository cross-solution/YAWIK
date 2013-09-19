<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Core\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Home site
     *
     */
    public function indexAction()
    { }
    
    public function mailAction() {
        $mail = $this->mail();
        
        $newMail1 = $mail->newMail();
        
        //$newMail1->setTemplate('aaa');
        //$newMail1->setSubject('Hallo');
        //$newMail1->setAdress('mork@ork');
        //$newMail1->setView(array('a' => 'b'));
        
        
        $response = $this->getResponse();
        
        return "test";
    }
    
}

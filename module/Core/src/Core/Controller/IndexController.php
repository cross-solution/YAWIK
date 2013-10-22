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
use Settings\Repository\Settings as SettingsRepository;
//use Settings\Repository\Settings;

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
    { 
        $acl = $this->getServiceLocator()->get('Acl');
        $check = $acl->isAllowed('user', 'route/auth-logout');
        
    }
    
    public function mailAction() {
        $ServiceLocator = $this->getServiceLocator();
        $settingsRepository = $ServiceLocator->get('RepositoryManager')->get('SettingsRepository');
        $userIdentity = $ServiceLocator->get('AuthenticationService')->getIdentity();
        $settingsEntity = $settingsRepository->getSettingsByUser($userIdentity);
        $settingsEntity->spawnAsEntities();
        
        $userRepository = $ServiceLocator->get('RepositoryManager')->get('user');
        
        $userEntity = $userRepository->find($userIdentity);
        $email = $userEntity->info->email;
        
        //$settingsEntity->application = array('mail' => 'TestMail');
        //$application = $settingsEntity->application->mail;
        
        $mail = $this->mail(array('Anrede'=>'Herr Sowieso'));
        
        //$mailer = $this->mailer();
        
        $mail->template('test');
        
        //$mail = $mailer->newMail();
        $mail->addTo('weitz@cross-solution.de');
        $mail->setBody('Sie sind jetzt im Cross Applicant Management angemeldet.');
        $mail->setFrom('cross@cross-solution.de', 'Cross Applicant Management');
        $mail->setSubject('Anmeldung');
        $result = $mail->send();
        
        $response = $this->getResponse();
        
        return "test";
    }
    
}

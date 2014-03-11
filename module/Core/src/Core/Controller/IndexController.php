<?php
/**
 * YAWIK
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
        $auth = $this->auth();
        if (!$auth->isLoggedIn()) {
            return;
        }
        
        $services = $this->getServiceLocator();
        $config   = $services->get('Config');
        
        $dashboardConfig = array(
            'controller' => 'Core\Controller\Index',
            'action'     => 'dashboard',
            'params'     => array()
        );
        
        if (isset($config['dashboard'])) {
            $dashboardConfig = array_merge(
                $dashboardConfig, 
                /** Intersect array to filter out invalid keys that might be in config */
                array_intersect_key($config['dashboard'], $dashboardConfig)
            );
        }
        
        extract($dashboardConfig); // $controller, $action, $params;
        $params['action'] = $action;
        
        return $this->forward()->dispatch($controller, $params);
        
    }
    
    public function dashboardAction()
    {
        $model = new ViewModel();
        $model->setTemplate('core/index/dashboard');
        
        $widgets = array();
        $modules = $this->getServiceLocator()->get('ModuleManager')->getLoadedModules();
        $widgets = array();
        foreach ($this->config('dashboard', array_keys($modules)) as $module => $cfg) {
            if (!isset($cfg['enabled']) || true !== $cfg['enabled']) {
                continue;
            }
            foreach ($cfg['widgets'] as $captureTo => $spec) {
                if (isset($spec['controller'])) {
                    $params = array('action' => 'dashboard');
                    if (isset($spec['params'])) {
                        $params = array_merge($params, $spec['params']);
                    }
                    
                    $viewModel = $this->forward()->dispatch($spec['controller'], $params);
                    // we ignore all errors and simply continue with the error template as widget content
                    $response = $this->getResponse();
                    if (200 != $response->getStatusCode()) {
                        $response->setStatusCode(200);
                        $viewModel = array(
                            'content' => 'Error loading widget.', 
                        );
                    }
                    if (!$viewModel instanceOf ViewModel) {
                        $viewModel = new ViewModel($viewModel);
                    }
                    if ($template = $viewModel->getTemplate()) {
                        $viewModel->setVariable('script', $template);
                    }
                } else if (isset($spec['script'])) {
                    $viewModel = new ViewModel(array('script' => $spec['script']));
                } else if (isset($spec['content'])) {
                    $viewModel = new ViewModel(array('content' => $spec['content']));
                }
            
                $viewModel->setTemplate('core/index/dashboard-widget.phtml');
                $model->addChild($viewModel, "dashboard_{$module}_{$captureTo}");
                //$widgets[] = $viewModel;
            }
        }
        //$model->setVariable('widgets', $widgets);
        return $model;
        
    }
    
    public function errorAction()
    {
        $viewModel = new ViewModel();
        $viewModel->setTemplate('error/index')
                  ->setVariable('message', 'An unexpected error had occured. Please try again later.');
        return $viewModel;
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
        $mail->setBody('Sie sind jetzt im YAWIK angemeldet.');
        $mail->setFrom('cross@cross-solution.de', 'YAWIK');
        $mail->setSubject('Anmeldung');
        $result = $mail->send();
        
        $response = $this->getResponse();
        
        return "test";
    }
    
}

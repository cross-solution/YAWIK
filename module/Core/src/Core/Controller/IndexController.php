<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Core\Controller;

use Core\Listener\DefaultListener;
use Interop\Container\ContainerInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

//use Settings\Repository\Settings;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.
 *
 */
class IndexController extends AbstractActionController
{
	/** @var  DefaultListener */
	private $defaultListener;
	
	private $config;
	
	/**
	 * @var ModuleManager
	 */
	private $moduleManager;
	
	public function __construct($defaultListener,$config,$moduleManager)
	{
		$this->defaultListener = $defaultListener;
		$this->config = $config;
		$this->moduleManager = $moduleManager;
	}
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
    	// @TODO: [ZF3] check if attach the default listener is really work
        parent::attachDefaultListeners();
        $events          = $this->getEventManager();
        $this->defaultListener->attach($events);
        return $this;
    }

    /**
     * Home site
     *
     */
    public function indexAction()
    {
        $auth = $this->Auth();
	    $config = $this->config;
        if (!$auth->isLoggedIn()) {
            
            if (array_key_exists('startpage', $config['view_manager']['template_map'])) {
                $this->layout()->setTerminal(true)->setTemplate('startpage');
            }
            return;
        }

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
        $modules = $this->moduleManager->getLoadedModules();
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
                    if (!$viewModel instanceof ViewModel) {
                        $viewModel = new ViewModel($viewModel);
                    }
                    if ($template = $viewModel->getTemplate()) {
                        $viewModel->setVariable('script', $template);
                    }
                } elseif (isset($spec['script'])) {
                    $viewModel = new ViewModel(array('script' => $spec['script']));
                } elseif (isset($spec['content'])) {
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
	
    static public function factory(ContainerInterface $container)
    {
	    $defaultListener = $container->get('DefaultListeners');
	    $config = $container->get('config');
	    return new static($defaultListener,$config,$container->get('ModuleManager'));
    }
}

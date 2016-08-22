<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\ControllerManager;
use Auth\Dependency\ModuleManager as Dependencies;

class RemoveController extends AbstractActionController
{
    /**
     * @var Dependencies
     */
    protected $dependencies;

    /**
     * @param ControllerManager $controllerManager
     * @return \Auth\Controller\RemoveController
     */
    public static function factory(ControllerManager $controllerManager)
    {
        $serviceManager = $controllerManager->getServiceLocator();
        
        return new static($serviceManager->get('Auth/Dependency/ModuleManager'));
    }
    
    /**
     * @param Dependencies $dependencies
     */
    public function __construct(Dependencies $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function indexAction()
    {
        $user = $this->serviceLocator->get('AuthenticationService')->getUser();
        
        if ($this->params()->fromPost('confirm'))
        {
            foreach ($this->dependencies as $dependency) /* @var $dependency \Auth\Dependency\ModuleInterface */
            {
                $dependency->removeItems($user);
            }
        }
        
        return [
            'dependencies' => $this->dependencies,
            'user' => $user,
            'router' => $this->getEvent()->getRouter()
        ];
    }
}

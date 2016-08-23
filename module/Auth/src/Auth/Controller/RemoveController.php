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
use Auth\Dependency\Manager as Dependencies;

class RemoveController extends AbstractActionController
{
    /**
     * @var Dependencies
     */
    protected $dependencies;

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
            $this->dependencies->removeItems($user);
        }
        
        return [
            'lists' => $this->dependencies->getLists(),
            'user' => $user,
            'router' => $this->getEvent()->getRouter()
        ];
    }
}

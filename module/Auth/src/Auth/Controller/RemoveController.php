<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Controller;

use Auth\Entity\Status;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Dependency\Manager as Dependencies;

/**
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>r
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.27
 */
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
        /* @var \Auth\AuthenticationService $auth */
        $auth = $this->serviceLocator->get('AuthenticationService');
        $user = $auth->getUser();
        $error = false;
        
        if ($this->params()->fromPost('confirm'))
        {
            if ($this->dependencies->removeItems($user)) {
                $auth->clearIdentity();
                $user->setStatus(Status::INACTIVE);
                return $this->redirect()->toRoute('lang');
            } else {
                $error = true;
            }
        }
        
        return [
            'lists' => $this->dependencies->getLists(),
            'user' => $user,
            'limit' => 20,
            'error' => $error,
        ];
    }
}

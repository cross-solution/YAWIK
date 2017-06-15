<?php


namespace Auth\Listener;

use Zend\Mvc\View\Http\ExceptionStrategy;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Auth\Exception\UserDeactivatedException;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class DeactivatedUserListener
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @author  fedys
 * @author  Anthonius Munthi <me@itstoni.com>
 *
 * @package Auth\Listener
 */
class DeactivatedUserListener extends ExceptionStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        parent::attach($events);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkDeactivatedUser']);
    }
    
    /**
     * @param MvcEvent $event
     */
    public function checkDeactivatedUser(MvcEvent $event)
    {
        $routeName = $event->getRouteMatch()
            ->getMatchedRouteName();
        $allowedRoutes = [
            'auth-logout'
        ];
        
        // do nothing for allowed routes
		if (in_array($routeName, $allowedRoutes))
        {
            return;
        }
        
        $auth = $event->getApplication()
            ->getServiceManager()
            ->get('AuthenticationService');
        
        // check for inactive user
        if ($auth->hasIdentity() && !$auth->getUser()->isActive()) {
            // set event error & throw dispach error
            $event->setError('User inactive');
            $event->setParam('exception', new UserDeactivatedException());
            return $event->getTarget()
                ->getEventManager()
                ->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event)
                ->last();
        }
    }

    /**
     * @see \Zend\Mvc\View\Http\ExceptionStrategy::prepareExceptionViewModel()
     */
    public function prepareExceptionViewModel(MvcEvent $event)
    {
        // do nothing if no error in the event
        $error = $event->getError();
        if (empty($error)) {
            return;
        }
    
        // do nothing if the result is a response object
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }
        
        // do nothing if there is no exception or the exception is not an UserDeactivatedException
        $exception = $event->getParam('exception');
        if (!$exception instanceof UserDeactivatedException) {
            return;
        }
        
        $auth = $event->getApplication()
            ->getServiceManager()
            ->get('AuthenticationService');
        
        // do nothing if no user is logged in or is active one
        if (!$auth->hasIdentity() || $auth->getUser()->isActive()) {
            return;
        }
      
        $response = $event->getResponse();
        if (!$response) {
            $response = new Response();
            $event->setResponse($response);
        }
        $response->setStatusCode(Response::STATUS_CODE_403);
        
        $model = new ViewModel([
            'message' => /*@translate*/ 'This user account has been disabled. Please contact the system adminstrator.',
            'exception' => $exception,
            'display_exceptions' => $this->displayExceptions(),
        ]);
        $model->setTemplate($this->getExceptionTemplate());
        $event->setResult($model);
    }
}

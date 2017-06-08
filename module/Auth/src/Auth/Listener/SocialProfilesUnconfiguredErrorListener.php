<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Listener to set the a specific error view template if login via social profiles
 * is not configured.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.23
 */
class SocialProfilesUnconfiguredErrorListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority=1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [ $this, 'onDispatchError']);

        return $this;
    }

    /**
     * Sets a specific view template if social network login is unconfigured.
     *
     * @param MvcEvent $e
     */
    public function onDispatchError(MvcEvent $e)
    {
        $ex = $e->getParam('exception');
        $model = $e->getResult();

        if ($model instanceof ViewModel
            && Application::ERROR_EXCEPTION == $e->getError()
            && 0 === strpos($ex->getMessage(), 'Your application id and secret')
        ) {
            $model->setTemplate('auth/error/social-profiles-unconfigured');
        }
    }
}

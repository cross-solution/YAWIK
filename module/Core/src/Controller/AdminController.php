<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller;

use Core\EventManager\EventManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Admin Dashboard controller.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since 0.25
 */
class AdminController extends AbstractActionController
{
    protected $adminControllerEvents;
    
    public function __construct(EventManager $eventManager)
    {
        $this->adminControllerEvents = $eventManager;
    }
    
    /**
     * Controls the admin dashboard page.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        /* @var \Core\EventManager\EventManager $events
         * @var AdminControllerEvent $event */
        $events = $this->adminControllerEvents;
        $event  = $events->getEvent(AdminControllerEvent::EVENT_DASHBOARD, $this);
        $events->triggerEvent($event);

        $model = new ViewModel();
        $widgets = [];
        foreach ($event->getViewModels() as $name => $child) {
            $model->addChild($child, $name);
            $widgets[] = $name;
        }

        $model->setVariable('widgets', $widgets);
        return $model;
    }
}

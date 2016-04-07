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

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class AdminController extends AbstractActionController
{

    public function indexAction()
    {
        /* @var \Core\EventManager\EventManager $events
         * @var AdminControllerEvent $event */
        $events = $this->getServiceLocator()->get('Core/AdminController/Events');
        $event  = $events->getEvent(AdminControllerEvent::EVENT_DASHBOARD, $this);
        $events->trigger($event);

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
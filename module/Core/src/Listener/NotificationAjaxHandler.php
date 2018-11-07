<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Listener;

use Core\Listener\Events\NotificationEvent;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class NotificationAjaxHandler
{
    protected $viewModel;
    protected $viewHelperManager;

    public function injectView(MvcEvent $event)
    {
        $viewModel = $event->getViewModel();
        $this->viewModel = $viewModel;
        if ($event->getApplication()->getServiceManager()->has('ViewHelperManager')) {
            $this->viewHelperManager = $event->getApplication()->getServiceManager()->get('ViewHelperManager');
        }
    }


    public function render(NotificationEvent $event)
    {
        if (isset($this->viewModel)) {
            if ($this->viewModel instanceof JsonModel) {
                // here add information for JSON
            } elseif ($this->viewModel instanceof ViewModel) {
                $headScript = $this->viewHelperManager->get('headScript');

                $notifications = $event->getTarget()->getNotifications();
                if (is_array($notifications) && !empty($notifications)) {
                    foreach ($notifications as $notification) {
                        $headScript->appendScript('/* ' . $notification->getNotification() . ' */');
                    }
                }
            }
        }
    }
}

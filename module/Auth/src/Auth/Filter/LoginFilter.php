<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Filter;

use Zend\Filter\FilterInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class LoginFilter implements FilterInterface, EventManagerAwareInterface
{

    protected $eventManager;

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(['Auth']);
        $this->eventManager = $eventManager;
        return $this;
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function filter($value = '')
    {
        $suffix = '';
        $e = new Event();
        $loginSuffixResponseCollection = $this->eventManager->trigger('login.getSuffix', $e);
        if (!$loginSuffixResponseCollection->isEmpty()) {
            $suffix = $loginSuffixResponseCollection->last();
        }
        return $value . $suffix;
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Acl\Assertion;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Skeleton for an EventManagerAware Assertion.
 *
 * Handles the management of the EventManager, the creation of the AssertionEvent, triggers the event and
 * introspecting the result set.
 *
 * Listeners to that event should return a simple boolean value. Other return types are treated as they were the
 * boolean TRUE. If a listener returns FALSE the propagation is stopped immediatly and no further listeners are invoked.
 * The assertion returns FALSE in this case.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.18
 */
abstract class AbstractEventManagerAwareAssertion implements EventManagerAwareInterface, AssertionInterface
{
    /**
     * The Event manager.
     *
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Identifiers for the SharedEventManager
     *
     * @var string[]
     */
    protected $identifiers = array();

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $identifiers = $this->getEventManagerIdentifiers() + array(
            __NAMESPACE__,
            __CLASS__,
            get_class($this),
            'Acl/Assertion',
        );

        $eventManager->setIdentifiers($identifiers);

        $this->events = $eventManager;
    }

    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * Gets the identifiers to be used by the SharedEventManager.
     *
     * Overwrite this to manipulate identifiers.
     *
     * @return string[]
     */
    protected function getEventManagerIdentifiers()
    {
        return $this->identifiers;
    }

    public function assert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        $preCheck = $this->preAssert($acl, $role, $resource, $privilege);

        if (is_bool($preCheck)) {
            return $preCheck;
        }

        $event = new AssertionEvent(null, $this);
        $event->setAcl($acl)
              ->setRole($role)
              ->setResource($resource)
              ->setPrivilege($privilege);
        
        $events = $this->getEventManager();

        $callback = function ($r) {
	        return false === $r;
        };
        
        $results = $events->triggerUntil(
        	$callback,
            $event->getName(),
            $event
        );

        return false === $results->last() ? false : true; // result must be BOOLEAN false (not "", null or 0 or any other value evaluated to FALSE)
    }

    /**
     * Overwrite this to check some conditions before the event is triggered.
     *
     * If this method returns a boolean value, this value will be returned as the assertions' result and
     * no event will be triggered.
     *
     * @param Acl               $acl
     * @param RoleInterface     $role
     * @param ResourceInterface $resource
     * @param null|string       $privilege
     *
     * @return null|bool
     */
    protected function preAssert(
        Acl $acl,
        RoleInterface $role = null,
        ResourceInterface $resource = null,
        $privilege = null
    ) {
        return null;
    }
}

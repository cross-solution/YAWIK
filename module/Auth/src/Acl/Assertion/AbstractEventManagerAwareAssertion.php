<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
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
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
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

    public function assert(Acl $acl,
                               RoleInterface $role = null,
                               ResourceInterface $resource = null,
                               $privilege = null
    ) {

        if ($this->preAssert($acl, $role, $resource, $privilege)) {

            $event = new AssertionEvent(null, $this);
            $event->setAcl($acl)
                  ->setRole($role)
                  ->setResource($resource)
                  ->setPrivilege($privilege);

            $events = $this->getEventManager();

            $results = $events->triggerUntil($event, function($r) { return false === $r; });

            return false === $results->last() ? false : true; // result must be BOOLEAN false (not "", null or 0)
        }

        return false;
    }

    /**
     * Overwrite this to check some conditions before the event is triggered.
     *
     * If this method returns <i>false</i>. No event is triggered and the assertion will fail!
     *
     * @param Acl               $acl
     * @param RoleInterface     $role
     * @param ResourceInterface $resource
     * @param null|string       $privilege
     *
     * @return bool
     */
    protected function preAssert(Acl $acl,
                               RoleInterface $role = null,
                               ResourceInterface $resource = null,
                               $privilege = null
    ) {
        return true;
    }

}
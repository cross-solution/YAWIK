<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Acl\Assertion;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Plugin manager for assertions.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AssertionManager extends AbstractPluginManager
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
    /**
     * Creates an instance.
     *
     * {@inheritDoc}
     *
     * Adds an additional initializer to inject an event manager to assertions
     * implementing {@link EventManagerAwareInterface}.
     *
     */
    public function __construct(ContainerInterface $container, ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->container = $container;

        // Pushing to bottom of stack to ensure this is done last
        $this->addInitializer(array($this, 'injectEventManager'), false);
    }

    /**
     * Injects a shared event manager aware event manager.
     *
     *
     * @param AssertionInterface      $assertion
     * @param AssertionManager $serviceLocator
     */
    public function injectEventManager($assertion, $serviceLocator)
    {
        /* @var $serviceLocator AssertionManager */

        if (!$assertion instanceof EventManagerAwareInterface) {
            return;
        }
	    $container = $this->container;
        $events = $assertion->getEventManager();
        if (!$events instanceof EventManagerInterface) {
            $events = $container->get('EventManager'); /* @var $events \Zend\EventManager\EventManagerInterface */
            $assertion->setEventManager($events);
        } else {
            $sharedEvents = $container->get('SharedEventManager'); /* @var $sharedEvents \Zend\EventManager\SharedEventManagerInterface */
            $events->setSharedManager($sharedEvents);
        }
    }

    /**
     * Validates assertions.
     *
     * Checks that the assertion implements AssertionInterface.
     *
     * @param mixed $plugin
     * @throws \RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof AssertionInterface) {
            throw new \RuntimeException('Expected plugin to be of type Assertion.');
        }
    }
}

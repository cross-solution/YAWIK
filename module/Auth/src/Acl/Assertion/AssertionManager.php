<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AssertionManager.php */ 
namespace Acl\Assertion;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AssertionManager
 * @package Acl\Assertion
 */
class AssertionManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        // Pushing to bottom of stack to ensure this is done last
        $this->addInitializer(array($this, 'injectEventManager'), false);
    }

    public function injectEventManager($assertion, ServiceLocatorInterface $serviceLocator)
    {
        if (!$assertion instanceOf EventManagerAwareInterface) {
            return;
        }

        $parentLocator = $serviceLocator->getServiceLocator();
        $events = $assertion->getEventManager();
        if (!$events instanceof EventManagerInterface) {
            $assertion->setEventManager($parentLocator->get('EventManager'));
        } else {
            $events->setSharedManager($parentLocator->get('SharedEventManager'));
        }
    }

    /**
     * @param mixed $plugin
     * @throws \RuntimeException
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf AssertionInterface) {
            throw new \RuntimeException('Expected plugin to be of type Assertion.');
        }
    }
}


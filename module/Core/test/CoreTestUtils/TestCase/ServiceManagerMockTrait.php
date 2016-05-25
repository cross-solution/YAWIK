<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\TestCase;

use CoreTestUtils\Mock\ServiceManager\Config as ServiceManagerMockConfig;
use CoreTestUtils\Mock\ServiceManager\PluginManagerMock;
use CoreTestUtils\Mock\ServiceManager\ServiceManagerMock;

/**
 * Creates a service manager mock with configured services.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
trait ServiceManagerMockTrait
{
    /**
     * The service manager mock instance
     *
     * @var ServiceManagerMock
     */
    private $serviceManagerMock;

    private $pluginManagerMock;

    public function tearDown()
    {
        $this->serviceManagerMock && $this->assertServiceManagerCallCount();
        $this->pluginManagerMock && $this->assertPluginManagerCallCount();
    }

    /**
     * Asserts that all expected calls to the service manager were made.
     *
     * @param int|null    $count
     * @param string|null $method
     * @param string|null $service
     */
    public function assertServiceManagerCallCount($count = null, $method = null, $service = null)
    {
        $mock = $this->getServiceManagerMock();
        if (null !== $count) {
            $mock->setExpectedCallCount($method, $service, $count);
            $mock->verifyCallCount($method, $service);

        } else {
            $mock->verifyCallCount();
        }
    }

    /**
     * Asserts that all expected calls to the plugin manager were made.
     *
     * @param int|null    $count
     * @param string|null $method
     * @param string|null $service
     * @param array|null $options
     */
    public function assertPluginManagerCallCount($count = null, $method = null, $service = null, $options = null)
    {
        $mock = $this->getPluginManagerMock();
        if (null !== $count) {
            $mock->setExpectedCallCount($method, $service, $options, $count);
            $mock->verifyCallCount($method, $service, $options);

        } else {
            $mock->verifyCallCount();
        }
    }


    /**
     * Gets or create the service manager mock.
     *
     * @param array $services
     *
     * @see ServiceManagerMockConfig::configureServiceManager
     * @return ServiceManagerMock
     */
    public function getServiceManagerMock(array $services = [])
    {
        if (!$this->serviceManagerMock) {
            $config                   = new ServiceManagerMockConfig(['mocks' => $services]);
            $this->serviceManagerMock = new ServiceManagerMock($config);
        }

        return $this->serviceManagerMock;
    }

    /**
     * Gets or create a plugin manager mock.
     *
     * @param array|\Zend\ServiceManager\ServiceLocatorInterface $services
     * @param null|int|\Zend\ServiceManager\ServiceLocatorInterface  $parent
     * @param int   $count
     *
     * @return PluginManagerMock
     */
    public function getPluginManagerMock($services = [], $parent = null, $count = 1)
    {

        if (!$this->pluginManagerMock) {
            if (is_array($services)) {
                $config = new ServiceManagerMockConfig(['mocks' => $services]);
            } else {
                $config = null;
                $count = is_int($parent) ? $parent : $count;
                $parent = $services;
            }

            $this->pluginManagerMock = new PluginManagerMock($config);

            if (null !== $parent) {
                $this->pluginManagerMock->setServiceLocator($parent, $count);
            }
        }

        return $this->pluginManagerMock;
    }
}
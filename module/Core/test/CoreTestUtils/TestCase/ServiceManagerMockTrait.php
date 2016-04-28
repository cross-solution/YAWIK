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

    public function tearDown()
    {
        $this->assertServiceManagerCallCount();
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
}
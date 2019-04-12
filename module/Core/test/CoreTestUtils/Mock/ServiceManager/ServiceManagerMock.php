<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTestUtils\Mock\ServiceManager;

use PHPUnit\Framework\TestCase;

use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceManager;

/**
 * Mock of a service manager.
 *
 * Allows tracking of method calls (with service name arguments).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @since  0.25
 */
class ServiceManagerMock extends ServiceManager
{
    protected $callCount = [
        'get' => [],
        'has' => [],
    ];

    protected $expectedCallCount = [];

    public function get($name, $usePeeringServiceManagers = true)
    {
        $this->incrementCallCount('get', $name);
    
        return parent::get($name, $usePeeringServiceManagers);
    }

    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = true)
    {
        if (is_string($name)) { // internally called with an array [normalizedName, requestedName].
            $this->incrementCallCount('has', $name);
        }

        return parent::has($name, $checkAbstractFactories, $usePeeringServiceManagers);
    }

    /**
     * Increment the call count.
     *
     * @param string $method
     * @param string $key
     * @param int    $count
     */
    protected function incrementCallCount($method, $key, $count = 1)
    {
        if (isset($this->callCount[$method][$key])) {
            $this->callCount[$method][$key] += $count;
        } else {
            $this->callCount[$method][$key] = $count;
        }
    }

    /**
     * Sets the expected call count.
     *
     * @param string          $method
     * @param string|null|int $key
     * @param int             $count
     */
    public function setExpectedCallCount($method, $key = null, $count)
    {
        if (is_int($key)) {
            $count = $key;
            $key   = null;
        }

        if (null === $key) {
            $key = '*';
        }
        $this->expectedCallCount[$method][$key] = $count;
    }

    /**
     * Verifies the call count.
     *
     * @param null|string $method
     * @param null|string $service
     *
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function verifyCallCount($method = null, $service = null)
    {
        foreach ($this->expectedCallCount as $methodName => $services) {
            if (null !== $method && $method != $methodName) {
                continue;
            }
            foreach ($services as $name => $count) {
                if (null !== $service && $service != $name) {
                    continue;
                }
                $actual = $this->getCallCount($methodName, $name);

                if ($actual != $count) {
                    throw new \PHPUnit_Framework_AssertionFailedError(
                        sprintf(
                            '%s::%s(%s) was expected to be called %d times, but was actually called %d times.',
                            get_class($this),
                            $methodName,
                            '*' == $name ? '' : $name,
                            $count,
                            $actual
                        )
                    );
                }
            }
        }
    }

    /**
     * Gets the actual call count
     *
     * @param string $method
     * @param null   $key
     *
     * @return int|number
     */
    public function getCallCount($method, $key = null)
    {
        if (null === $key || '*' == $key) {
            return array_sum($this->callCount[$method]);
        }

        return isset($this->callCount[$method][$key]) ? $this->callCount[$method][$key] : 0;
    }
}

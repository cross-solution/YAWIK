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

use Zend\ServiceManager\Config as ZfConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * Configures a ServiceManagerMock
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class Config extends ZfConfig
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->config = array_merge($this->config, $config);
    }
    
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        /* @var ServiceManagerMock $serviceManager */
        parent::configureServiceManager($serviceManager);
        
        foreach ($this->getMocks() as $name => $spec) {
            if (is_array($spec) && array_key_exists('service', $spec)) {
                if (isset($spec['count_get'])) {
                    $serviceManager->setExpectedCallCount('get', $name, $spec['count_get']);
                }
                if (isset($spec['count_has'])) {
                    $serviceManager->setExpectedCallCount('has', $name, $spec['count_has']);
                }
                if (isset($spec['direct'])) {
                    $serviceManager->setService($name, $spec['service']);
                    continue;
                }

                $spec = $spec['service'];
            }

            if (is_object($spec)) {
                $serviceManager->setService($name, $spec);
            } elseif (is_string($spec)) {
                $serviceManager->setInvokableClass($name, $spec);
            } else {
                $factory = new CreateInstanceFactory($spec[0], isset($spec[1]) ? $spec[1] : []);
                $serviceManager->setFactory($name, $factory);
            }
        }
    }

    /**
     * Gets the mocks configuration.
     *
     * @return array
     */
    public function getMocks()
    {
        return isset($this->config['mocks']) ? $this->config['mocks'] : [];
    }
}

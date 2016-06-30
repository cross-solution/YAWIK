<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTestUtils\TestCase;

use CoreTestUtils\InstanceCreator;
use Zend\Stdlib\ArrayUtils;

/**
 * Trait for setup the SUT instance.
 *
 * Allows the automated creation of the SUT instance by providing a property $target with the
 * following specs:
 *
 * * a simple string: It's treated as FQCN and the class will be instantiated.
 *
 * * an enumerated array where the first entry is the FQCN and the second entry is an
 *   array of constructor arguments.
 *
 *   You may provide instances in the args array with the following syntax:
 *      '@FQCN' or
 *      ['@FQCN', [ args ]] (args itself can contain instance syntax.)
 *
 *  if the second entry is a string, a method with this name will be called on the TestCase instance
 *  which should return the array of arguments.
 *
 * * Full specification:
 *
 * [
 *      // Call a method on the TestCase instance to get the target
 *      'method' => 'methodName',
 *
 *      // FQCN of the target class
 *      'class' => FQCN,
 *
 *      // Arguments provided by an TestCase method
 *      'args' => 'method',
 *
 *      // Arguments as Array
 *      'args'  => [ 'arg', '@FQCN', ['@FQCN', ['args'] ], (recursive class instantiation)
 *
 *      //
 *      //override specs for specific tests:
 *      //
 *
 *      // Do not setup target.
 *      '@testName' => false,
 *
 *      // Overrife 'method' spec:
 *      '@testName' => 'method',
 *
 *
 *      '@testName' => [
 *
 *          // Override 'class' and 'args', when needed.
 *
 *          // generate a mock from the target class
 *          'mock' = [ mockedMethod, ... ]
 *
 *          'mock' => [ mockedMethod => ['expects' => int, 'with' => [], 'return' => mixed ],
 *      ]
 * ]
 *
 * If you redefine the method "setup" in your test case, you need to
 * call "setupTargetInstance()" manually.
 *
 *
 *
 * @property string|array|object $target
 *
 * @method string getName()
 * @method \PHPUnit_Framework_MockObject_MockBuilder getMockBuilder()
 * @method exactly()
 * @method any()
 * @method returnSelf()
 * @method returnValue()
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25 /refactored in 0.26
 */
trait SetupTargetTrait
{
    public function setUp()
    {
        $this->setupTargetInstance();
    }

    /**
     * Creates an instance of the target class.
     *
     */
    public function setupTargetInstance()
    {
        if (!property_exists($this, 'target')) {
            return;
        }

        $spec = $this->target;

        if (is_string($spec)) {
            $this->target = new $spec();
            return;
        }

        $testName = $this->getName(false);
        $testNameKey = "@$testName";

        if (!is_array($spec) || (isset($spec[$testNameKey]) && false === $spec[$testNameKey])) {
            return;
        }

        if (isset($spec[0])) {
            $spec['class'] = $spec[0];
         }

        if (!isset($spec['args'])) {
            $spec['args'] = isset($spec[1]) ? $spec[1] : [];
        }

        if (isset($spec[$testNameKey])) {
            if (is_string($spec[$testNameKey])) {
                $this->target = $this->{$spec[$testNameKey]}();
                return;
            }

            /* Override specs for specific test */
            foreach ($spec[$testNameKey] as $key => $value) {
                $spec[$key] = $value;
            }
        }

        if (isset($spec['method'])) {
            $this->target = $this->{$spec['method']}();
            return;
        }

        if (is_string($spec['args'])) {
            $spec['args'] = $this->{$spec['args']}();
        }

        if (isset($spec['mock'])) {
            $this->target = $this->_setupTarget_setupMock($spec['class'], $spec['args'], $spec['mock']);
            return;
        }

        $this->target = InstanceCreator::newClass($spec['class'], $spec['args']);
    }

    /**
     * Creates a mock from the target class
     *
     * @param string $class
     * @param array $args
     * @param array $spec
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _setupTarget_setupMock($class, $args, $spec)
    {
        /*
         * $spec = ['method', 'method', ...]
         * $spec = [
         *      'method' => int,
         *      'method' => [ 'count' => int, 'with' => mixed, 'return' => mixed ]
         * ],
         *
         */

        $methods = [];
        $methodMocks  = [];

        foreach ($spec as $method => $methodSpec) {
            if (is_int($method)) {
                $methods[] = $methodSpec;
                continue;
            }

            $methods[] = $method;
            $methodMocks[$method] = [
                'expects' => isset($methodSpec['count']) ? $this->exactly($methodSpec['count']) : $this->any(),
                'with' => isset($methodSpec['with']) ? $methodSpec['with'] : null,
                'return' => isset($methodSpec['return'])
                        ? ('__self__' == $methodSpec['return'] ? $this->returnSelf() : $this->returnValue($methodSpec['return']))
                        : null
            ];
        }

        $mockBuilder = $this
            ->getMockBuilder($class)
            ->setMethods($methods);


        if (false === $args) {
            $mockBuilder->disableOriginalConstructor();
        } else {
            $mockBuilder->setConstructorArgs(InstanceCreator::mapArray($args));
        }

        $mock = $mockBuilder->getMock();

        foreach ($methodMocks as $method => $mockSpec) {
            $methodMock = $mock->expects($mockSpec['expects'])->method($method);

            if ($mockSpec['with']) {
                $methodMock->with($mockSpec['with']);
            }

            if ($mockSpec['return']) {
                $methodMock->will($mockSpec['return']);
            }
        }

        return $mock;
    }
    
}
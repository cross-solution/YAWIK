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

use PHPUnit\Framework\TestCase;

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
 *      // Get a \ReflectionClass instance instead of the class instance
 *      'as_reflection' => true,
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
 *      // Use a class for a test:
 *      '@testName' => 'FQCN',
 *
 *
 *      '@testName' => [
 *
 *          // Override 'class' and 'args', when needed. (or use key 0 and 1)
 *          // 'as_reflection' can also be set per testCase here
 *
 *          // generate a mock from the target class
 *          'mock' = [ mockedMethod, ... ]
 *
 *          'mock' => [ mockedMethod => ['expects' => int, 'with' => [], 'return' => mixed ],
 *      ]
 *
 *      //
 *      // Override specs for specific tests AND datasets:
 *      //
 *
 *      '@testName|setName' => ...
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
 * @method \PHPUnit_Framework_MockObject_MockBuilder getMockBuilder(string $className)
 * @method exactly()
 * @method any()
 * @method returnSelf()
 * @method returnValue()
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25 /refactored in 0.26
 * @since 0.29 added 'as_reflection' option.
 */
trait SetupTargetTrait
{
    protected function setUp(): void
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
            if (class_exists($spec)) {
                $this->target = new $spec();
            } else {
                $this->target = $this->$spec();
            }

            return;
        }

        $testNameParts = explode(' ', $this->getName());
        $testName = array_shift($testNameParts);
        $testSet  = trim(array_pop($testNameParts), '"');

        $testNameKey = '@' . $testName;
        $testNameSetKey = $testNameKey . '|' . $testSet;
        $testSpec = isset($spec[$testNameKey]) ? $spec[$testNameKey] : (isset($spec[$testNameSetKey]) ? $spec[$testNameSetKey] : null);

        if (null === $testSpec) {
            foreach (array_keys($spec) as $testSpecPattern) {
                if (false === strpos($testSpecPattern, '*')) {
                    continue;
                }

                if (preg_match('~^' . str_replace('*', '.*', substr($testSpecPattern, 1)) . '~', $testName)) {
                    $testSpec = $spec[$testSpecPattern];
                    break;
                }
            }
        }

        if (!is_array($spec) || false === $testSpec) {
            $this->target = null;
            return;
        }

        if (null !== $testSpec) {
            if (is_string($testSpec)) {
                if (isset($spec[$testSpec])) {
                    $testSpec = $spec[$testSpec];
                } elseif (class_exists($testSpec)) {
                    $testSpec = [ $testSpec ];
                } else {
                    $this->target = $this->{$testSpec}();
                    return;
                }
            }

            /* Override specs for specific test */
            foreach ($testSpec as $key => $value) {
                if ('ignore' === $key || 'unset' === $key) {
                    foreach ((array) $value as $ignKey) {
                        unset($spec[$ignKey]);
                    }
                    continue;
                }

                $spec[$key] = $value;
            }
        }

        if (isset($spec['pre']) && is_string($spec['pre'])) {
            $this->{$spec['pre']}();
        }

        if (isset($spec['method'])) {
            $this->target = $this->{$spec['method']}();
            return;
        }

        if (!isset($spec['class'])) {
            if (!isset($spec[0])) {
                throw new \PHPUnit_Framework_Exception(__TRAIT__ . ': No target class name specified.');
            }
            $spec['class'] = $spec[0];
        }

        if (false === $spec['class']) {
            $this->target = null;
            return;
        }

        if (!class_exists($spec['class']) && !isset($spec['method'])) {
            $spec['method'] = $spec['class'];
        }

        if (isset($spec['as_reflection']) && $spec['as_reflection']) {
            $this->target = new \ReflectionClass($spec['class']);
            return;
        }

        if (!isset($spec['args'])) {
            $spec['args'] = isset($spec[1]) ? $spec[1] : [];
        }

        if (is_string($spec['args'])) {
            $spec['args'] = $this->{$spec['args']}();
        }


        $this->target = isset($spec['mock'])
        ? $this->_setupTarget_setupMock($spec['class'], $spec['args'], $spec['mock'])
        : $this->target = InstanceCreator::newClass($spec['class'], $spec['args']);

        if (isset($spec['post']) && is_string($spec['post'])) {
            $this->{$spec['post']}();
        }
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


        if (is_string($spec)) {
            $mock = $this->$spec();
        } else {
            $call = function ($spec) {
                $cb   = [$this, $spec[0]];
                $args = isset($spec[1]) ? (array) $spec[1] : [];

                return call_user_func_array($cb, $args);
            };

            $methods = [];
            $methodMocks  = [];

            if (is_array($spec)) {
                foreach ($spec as $method => $methodSpec) {
                    if (is_int($method)) {
                        $methods[] = $methodSpec;
                        continue;
                    }

                    if (is_string($methodSpec)) {
                        $methodSpec = $this->$methodSpec();
                    } elseif (is_int($methodSpec)) {
                        $methodSpec = ['count' => $methodSpec];
                    }

                    $methods[] = $method;
                    $methodMocks[$method] = [
                        'expects' => isset($methodSpec['count']) ? $this->exactly($methodSpec['count']) : $this->any(),
                        'with'    => isset($methodSpec['@with'])
                                     ? $call((array) $methodSpec['@with'])
                                     : (isset($methodSpec['with']) ? $methodSpec['with'] : null),
                        'return'  => isset($methodSpec['@return'])
                                     ? $call((array) $methodSpec['@return'])
                                     : (
                                         isset($methodSpec['return'])
                                        ? ('__self__' === $methodSpec['return'] ? $this->returnSelf() : $this->returnValue($methodSpec['return']))
                                        : null
                                       ),
                    ];
                }
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
                    if (!is_array($mockSpec['with'])) {
                        $mockSpec['with'] = [$mockSpec['with']];
                    }

                    call_user_func_array([$methodMock, 'with'], $mockSpec['with']);
                }


                if ($mockSpec['return']) {
                    $methodMock->will($mockSpec['return']);
                }
            }
        }

        return $mock;
    }
}

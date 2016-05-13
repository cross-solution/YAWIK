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

use Zend\Stdlib\ArrayUtils;

/**
 * Trait for setup the SUT instance.
 *
 * Allows the automated creation of the SUT instance in the following ways:
 *
 * * define a property $target and assign a FQCN string to it:
 *      The specified class will be instatiated.
 *      If you redefined the method getTargetArgs() and it returns an array,
 *      then the SUT instance will be created as if you provided an array (see below).
 *      (This allows for complex constructor arguments.)
 *
 * * define a property $target and assign an array to it:
 *      The first item must be the FQCN as string,
 *      the second item must be an array with constructor arguments.
 *      The specified class' constructor will be called with the arguments.
 *
 * If you redefine the method "setup" in your test case, you need to
 * call "setupTargetInstance()" manually.
 *
 *
 *
 * @property string|array|object $target
 * @property array $targetExclude
 * @property array $targetMock
 * @method string getName()
 * @method \PHPUnit_Framework_MockObject_MockBuilder getMockBuilder()
 * @method assertAttributeSame()
 * @method exactly()
 * @method any()
 * @method returnSelf()
 * @method returnValue()
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25
 */
trait SetupTargetTrait
{
    public function setUp()
    {
        $this->setupTargetInstance();
    }

    public function setupTargetInstance()
    {
        if (!property_exists($this, 'target')) {
            return;
        }

        $testName = $this->getName(false);
        $spec = $this->target;


        if (is_string($spec)) {
            $spec = [
                'class' => $spec
            ];
        } else if (!is_array($spec) || (isset($spec['exclude']) && in_array($testName, $spec['exclude']))) {
            return;
        }

        if (!isset($spec['args'])) {
            $spec['args'] = $this->getTargetArgs();
        }

        if (isset($spec['mock'][$testName])) {
            $this->target = $this->setupTargetMock($spec['class'], $spec['args'], $spec['mock'][$testName]);
            return;
        }

        $this->target = $this->setupTargetCreateInstance($spec['class'], $spec['args']);
    }

    protected function getTargetArgs()
    {
        return [];
    }

    private function setupTargetCreateInstance($class, $args)
    {
        if (empty($args)) {
            return new $class();
        }

        $checkProperties = array_filter(array_keys($args), 'is_string');
        $args = array_map([$this, 'setupTargetCreateInstances'], $args);

        $reflection = new \ReflectionClass($class);
        $instance = $reflection->newInstanceArgs($args);

        foreach ($checkProperties as $property) {
            $this->assertAttributeSame($args[$property], $property, $instance);
        }

        return $instance;
    }

    private function setupTargetCreateInstances($item)
    {
        if (is_string($item) && 0 === strpos($item, '@')) {
            $class = substr($item, 1);
            return new $class;
        }

        if (ArrayUtils::isList($item) && 2 == count($item) && is_string($item[0]) && 0 === strpos($item[0], '@')) {
            return $this->setupTargetCreateInstance(substr($item[0],1), $item[1]);
        }

        return $item;
    }

    private function setupTargetMock($class, $args, $spec)
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
            $mockBuilder->setConstructorArgs(array_map([$this, 'setupTargetCreateInstances'], $args));
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
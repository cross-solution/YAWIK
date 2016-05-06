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

            $mockBuilder = $this
                ->getMockBuilder($spec['class'])
                ->setMethods($spec['mock'][$testName]);

            if (false === $spec['args']) {
                $mockBuilder->disableOriginalConstructor();
            } else {
                $mockBuilder->setConstructorArgs($spec['args']);
            }

            $this->target = $mockBuilder->getMock();
            return;
        }

        if (empty($spec['args'])) {
            $this->target = new $spec['class']();
        } else {
            $reflection = new \ReflectionClass($spec['class']);
            $this->target = $reflection->newInstanceArgs($spec['args']);
        }
    }

    protected function getTargetArgs()
    {
        return [];
    }
    
}
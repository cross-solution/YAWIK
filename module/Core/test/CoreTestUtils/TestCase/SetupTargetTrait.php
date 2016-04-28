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
        if (!property_exists($this, 'target')) { return; }

        if (is_string($this->target)) {
            $class = $this->target;
            $args = $this->getTargetArgs();

        } else if (is_array($this->target)) {
            $class = $this->target[0];
            $args  = $this->target[1];

        } else {
            return;
        }

        if ($args) {
            $reflection = new \ReflectionClass($class);
            $this->target = $reflection->newInstanceArgs($args);

        } else {
            $this->target = new $class();
        }
    }

    protected function getTargetArgs()
    {
        return false;
    }
    
}
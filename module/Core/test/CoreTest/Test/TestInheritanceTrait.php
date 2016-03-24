<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Test;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait TestInheritanceTrait
{


    public function testExtendsCorrectParentClass()
    {
        if (!isset($this->parentClass) || !isset($this->target)) {
            trigger_error(__TRAIT__ . ' expects properties "parentClass" and "target" to be set.', E_USER_NOTICE);
            return;
        }

        $this->assertInstanceOf($this->parentClass, $this->target);
    }

    public function testImplementsRequiredInterfaces()
    {
        if (!isset($this->interfaces) || !isset($this->target)) {
            trigger_error(__TRAIT__ . ' expects properties "interfaces" to be set.', E_USER_NOTICE);
            return;
        }

        if (false === $this->interfaces) {
            return;
        }

        foreach ($this->interfaces as $interface) {
            $this->assertInstanceOf($interface, $this->target);
        }
    }
}
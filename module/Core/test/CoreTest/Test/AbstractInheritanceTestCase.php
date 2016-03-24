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
abstract class AbstractInheritanceTestCase extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    protected $target;

    protected $parentClass = false;

    protected $interfaces = false;

    public function setUp()
    {
        if (is_string($this->target)) {
            $this->target = new $this->target;
        } else {
            $this->setupTarget();
        }
    }

    public function setupTarget()
    {

    }
}
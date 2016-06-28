<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTestUtils\Constraint;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class UsesTraits extends \PHPUnit_Framework_Constraint
{
    private $expectedTraits = [];
    private $result = [];

    public function __construct($expectedTraits = [])
    {
        $this->expectedTraits = (array) $expectedTraits;
        parent::__construct();
    }

    public function count()
    {
        return count($this->expectedTraits);
    }

    protected function matches($other)
    {
        $traits       = class_uses($other);
        $result       = array_diff($this->expectedTraits, $traits);
        $this->result = $result;

        return empty($result);
    }

    protected function failureDescription($other)
    {
        return (is_string($other) ? $other : get_class($other)) . ' ' . $this->toString();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        $traits = '';

        foreach ($this->expectedTraits as $trait) {
            $traits .= PHP_EOL . ' ['
                     . (in_array($trait, $this->result) ? ' ' : 'x')
                     . '] ' . $trait;
        }

        return 'uses traits: ' . $traits;
    }


}
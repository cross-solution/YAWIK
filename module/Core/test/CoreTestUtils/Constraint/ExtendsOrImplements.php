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
class ExtendsOrImplements extends \PHPUnit_Framework_Constraint
{
    private $parentsAndInterfaces = [];
    private $result = [];

    public function __construct($parentsAndInterfaces = [])
    {
        $this->parentsAndInterfaces = (array) $parentsAndInterfaces;
        parent::__construct();
    }

    public function count()
    {
        return count($this->parentsAndInterfaces);
    }

    protected function matches($other)
    {
        $this->result = [];

        foreach ($this->parentsAndInterfaces as $fqcn) {
            $this->result[$fqcn] = $other instanceOf $fqcn;
        }

        return array_reduce($this->result, function($carry, $value) { return $carry && $value; }, true);
    }

    protected function failureDescription($other)
    {
        return get_class($other) . ' ' . $this->toString();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        $inheritance = '';

        foreach ($this->result as $fqcn => $valid) {
            $inheritance .= PHP_EOL . ' ['
                     . ($valid ? 'x' : ' ')
                     . '] ' . $fqcn;
        }

        return 'extends or implements: ' . $inheritance;
    }


}
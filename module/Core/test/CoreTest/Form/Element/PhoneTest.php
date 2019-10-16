<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace CoreTest\Form\Element;

use PHPUnit\Framework\TestCase;

use Core\Form\Element\Phone;
use Zend\Validator\Regex as RegexValidator;

/**
* @covers \Core\Form\Element\Phone
*/
class PhoneTest extends TestCase
{
    /**
     * @var Phone
     */
    protected $target;

    protected function setUp(): void
    {
        $this->target = new Phone();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Core\Form\Element\Phone', $this->target);
        $this->assertInstanceOf('Zend\Form\Element', $this->target);
    }

    public function testSetGetValidator()
    {
        $input = new RegexValidator('/^([\+][0-9]{1,3}[ \.\-])?([\(]{1}[0-9]{1,6}[\)])?([0-9 \.\-\/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/');
        $this->target->setValidator($input);
        $this->assertSame($this->target->getValidator(), $input);
    }

    public function testGetInputSpecification()
    {
        $inputSpecification = $this->target->getInputSpecification();
        $this->assertSame(
            $inputSpecification,
            ['name' => $this->target->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => array(
                $this->target->getValidator(),
            )]
        );
    }
}

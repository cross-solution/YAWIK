<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form\InputFilter;

use Jobs\Entity\AtsModeInterface;
use Jobs\Form\InputFilter\AtsMode;

/**
 * Tests for AtsMode input filter
 *
 * @covers \Jobs\Form\InputFilter\AtsMode
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 * @group Jobs.Form.InputFilter
 */
class AtsModeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Extends \Zend\InputFilter\InputFilter
     */
    public function testExtendsInputFilter()
    {
        $this->assertInstanceOf('\Zend\InputFilter\InputFilter', new AtsMode());
    }

    public function provideAddsValidatorsTestData()
    {
        return array(
            array(
                array('mode' => AtsModeInterface::MODE_NONE),
                false
            ),
            array(
                array('mode' => AtsModeInterface::MODE_INTERN),
                false
            ),
            array(
                array('mode' => AtsModeInterface::MODE_URI),
                array(
                    'name' => 'uri',
                    'validators' => array(
                        array(
                            'name' => 'uri',
                            'options' => array(
                                'allowRelative' => false,
                            ),
                        ),
                    ),
                )
            ),
            array(
                array('mode' => AtsModeInterface::MODE_EMAIL),
                array(
                    'name' => 'email',
                    'validators' => array(
                        array('name' => 'EmailAddress')
                    ),
                )
            )
        );
    }

    /**
     * @testdox adds validators dynamically upon setting the data to validate
     * @dataProvider provideAddsValidatorsTestData
     *
     * @param array $data Mocked data to test
     * @param bool|array $expectedSpec false: Nothing should be added. Array: Spec with what add should be called.
     */
    public function testAddsValidators($data, $expectedSpec)
    {
        /* @var $target AtsMode|\PHPUnit_Framework_MockObject_MockObject */
        $target = $this->getMockBuilder('\Jobs\Form\InputFilter\AtsMode')
                       ->disableOriginalConstructor()
                       ->setMethods(array('add', 'populate'))
                       ->getMock();

        if (false === $expectedSpec) {
            $target->expects($this->never())->method('add');
        } else {
            $target->expects($this->once())->method('add')->with($expectedSpec);
        }

        $target->setData($data);
    }


}
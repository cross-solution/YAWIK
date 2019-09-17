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

use PHPUnit\Framework\TestCase;

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
class AtsModeTest extends TestCase
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
        return [
            [
                [
                    'mode' => AtsModeInterface::MODE_NONE,
                    'oneClickApply' => true
                ],
                [
                    [
                        [
                            'name' => 'oneClickApplyProfiles',
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                [
                    'mode' => AtsModeInterface::MODE_INTERN,
                    'oneClickApply' => false
                ],
                [
                    [
                        [
                            'name' => 'oneClickApplyProfiles',
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                [
                    'mode' => AtsModeInterface::MODE_INTERN,
                    'oneClickApply' => true
                ],
                [
                    [
                        [
                            'name' => 'oneClickApplyProfiles',
                            'required' => true
                        ]
                    ]
                ]
            ],
            [
                [
                    'mode' => AtsModeInterface::MODE_URI,
                    'oneClickApply' => false
                ],
                [
                    [
                        [
                            'name' => 'uri',
                            'validators' => [
                                [
                                    'name' => 'uri',
                                    'options' => [
                                        'allowRelative' => false
                                    ]
                                ]
                            ],
                            'filters'  => [
                                ['name' => 'StripTags'],
                            ]
                        ]
                    ],
                    [
                        [
                            'name' => 'oneClickApplyProfiles',
                            'required' => false
                        ]
                    ]
                ]
            ],
            [
                [
                    'mode' => AtsModeInterface::MODE_EMAIL,
                    'oneClickApply' => true
                ],
                [
                    [
                        [
                            'name' => 'email',
                            'validators' => [
                                [
                                    'name' => 'EmailAddress'
                                ]
                            ]
                        ]
                    ],
                    [
                        [
                            'name' => 'oneClickApplyProfiles',
                            'required' => false
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @testdox adds validators dynamically upon setting the data to validate
     * @dataProvider provideAddsValidatorsTestData
     *
     * @param array $data Mocked data to test
     * @param array $expectedSpec Spec with what add should be called
     */
    public function testAddsValidators($data, $expectedSpec)
    {
        /* @var $target AtsMode|\PHPUnit_Framework_MockObject_MockObject */
        $target = $this->getMockBuilder('\Jobs\Form\InputFilter\AtsMode')
                       ->disableOriginalConstructor()
                       ->setMethods(array('add', 'populate'))
                       ->getMock();

        $add = $target->expects($this->exactly(count($expectedSpec)))
            ->method('add');
        call_user_func_array([$add, 'withConsecutive'], $expectedSpec);

        $target->setData($data);
    }
}

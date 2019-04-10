<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Form\InputFilter;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Form\InputFilter\Education;
use Zend\InputFilter\InputFilter;

/**
 * Tests for \Cv\Form\InputFilter\Education
 *
 * @covers \Cv\Form\InputFilter\Education
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 * @group Cv.Form.InputFilter
 */
class EducationTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|Education|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        Education::class,
        '@testSetData' => [
            'mock' => [ 'add', 'populate' /* is called in parent method */ ]
        ]
    ];

    private $inheritance = [ InputFilter::class ];

    public function provideFilterData()
    {
        return [
            [ ['currentIndicator' => true ] ],
            [ ['currentIndicator' => false ] ]
        ];
    }

    /**
     * @dataProvider provideFilterData
     *
     * @param $data
     */
    public function testSetData($data)
    {
        $expectedAddArg = [
            'name' => 'endDate',
            'required' => !$data['currentIndicator']
        ];

        $this->target->expects($this->once())->method('add')->with($expectedAddArg);

        $this->target->setData($data);
    }
}

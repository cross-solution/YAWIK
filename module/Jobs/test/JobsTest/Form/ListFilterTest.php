<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Form\ListFilter;

/**
* @covers \Jobs\Form\ListFilter
*/
class ListFilterTest extends TestCase
{
    public function testConstructor()
    {
        $testedObject = new ListFilter();
        $this->assertInstanceOf('Jobs\Form\Listfilter', $testedObject);
        $this->assertAttributeEquals('Jobs/ListFilterBaseFieldset', 'fieldset', $testedObject);
    }

    public function testSetGetPartial()
    {
        $options = ['fieldset' => 'Jobs/ListFilterLocationFieldset'];
        $testedObject = new ListFilter(null, $options);
        $this->assertEquals($testedObject->getViewPartial(), 'jobs/form/list-filter');
        $input = "viewpartial";
        $testedObject->setViewPartial($input);
        $this->assertEquals($testedObject->getViewPartial(), $input);
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Jobs\Form\ListFilter')
                       ->setMethods(array('add', 'setName', 'setAttributes'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = [
            'type'    => 'Jobs/ListFilterBaseFieldset',
            'options' => ['use_as_base_fieldset' => false]
        ];
        $add2 = [
            'type' => 'Core/ListFilterButtons'
        ];

        $target->expects($this->exactly(2))
               ->method('add')
               ->withConsecutive(
                   array($add1),
                   array($add2)
               )->will($this->returnSelf());

        $target->expects($this->once())->method('setName')->with('jobs-list-filter');
        $target->expects($this->once())
            ->method('setAttributes')
            ->with([
                       'id' => 'jobs-list-filter',
                       'data-handle-by' => 'native'
                   ]);
        /* @var $target \PHPUnit_Framework_MockObject_MockObject|\Jobs\Form\ListFilter */
        $target->init();
    }
}

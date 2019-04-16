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

use Jobs\Form\ListFilterBaseFieldset;
use Jobs\Entity\Status;

/**
* @covers \Jobs\Form\ListFilterBaseFieldset
*/
class ListFilterBaseTest extends TestCase
{
    public function testConstructor()
    {
        $target= new ListFilterBaseFieldset();
        $this->assertInstanceOf('Jobs\Form\ListFilterBaseFieldset', $target);
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Jobs\Form\ListFilterBaseFieldset')
                       ->setMethods(array('add', 'setName'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = ['type' => 'Hidden',
                 'name' => 'page',
                 'attributes' => ['value' => 1,]
        ];

        $add2 = [
            'name' => 'search',
            'options' => [
                'label' => /*@translate*/ 'Job title',
            ],
        ];

        $target->expects($this->exactly(2))
               ->method('add')
               ->withConsecutive(
                   [$add1],
                   [$add2]
               )->will($this->returnSelf());

        /* @var $target \PHPUnit_Framework_MockObject_MockObject|\Jobs\Form\ListFilterLocationFieldset */
        $target->init();
    }
}

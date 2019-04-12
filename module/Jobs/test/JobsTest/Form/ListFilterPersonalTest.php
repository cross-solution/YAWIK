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

use Jobs\Form\ListFilterPersonalFieldset;
use Jobs\Entity\Status;

/**
* @covers \Jobs\Form\ListFilterPersonalFieldset
*/
class ListFilterPersonalTest extends TestCase
{
    public function testConstructor()
    {
        $target= new ListFilterPersonalFieldset();
        $this->assertInstanceOf('Jobs\Form\ListFilterPersonalFieldset', $target);
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Jobs\Form\ListFilterPersonalFieldset')
                       ->setMethods(array('add', 'parentInit'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = ['type'       => 'Radio',
                 'name'       => 'by',
                 'options'    => array(
                     'value_options' => array(
                         'all' => /*@translate*/ 'Show all jobs',
                         'me'  => /*@translate*/ 'Show my jobs',
                     ),
                 ),
                 'attributes' => array(
                     'value' => 'all',
                 )
        ];

        $add2 = [
            'type'       => 'Radio',
            'name'       => 'status',
            'options'    => array(
                'value_options' => array(
                    'all' => /*@translate*/ 'All',
                    Status::ACTIVE => /*@translate*/ 'Active',
                    Status::INACTIVE => /*@translate*/ 'Inactive',
                )
            ),
            'attributes' => array(
                'value' => 'all',
            )
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

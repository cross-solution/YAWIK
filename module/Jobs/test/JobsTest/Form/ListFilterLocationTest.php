<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Form;

use Jobs\Form\ListFilterLocationFieldset;

/**
* @covers \Jobs\Form\ListFilter
*/
class ListFilterLocationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $target= new ListFilterLocationFieldset();
        $this->assertInstanceOf('Jobs\Form\ListFilterLocationFieldset', $target);
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Jobs\Form\ListFilterLocationFieldset')
                       ->setMethods(array('add', 'parentInit'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = [
            'name'    => 'l',
            'type'    => 'Location',
            'options' => array(
                'label'       => /*@translate*/
                    'Location',
                'engine_type' => null
            ),
        ];

        $add2 = [
            'name'       => 'd',
            'type'       => 'Zend\Form\Element\Select',
            'options'    => array(
                'label'         => /*@translate*/
                    'Distance',
                'value_options' => [
                    '5'   => '5 km',
                    '10'  => '10 km',
                    '20'  => '20 km',
                    '50'  => '50 km',
                    '100' => '100 km'
                ],

            ),
            'attributes' => [
                'value'            => '10', // default distance
                'data-searchbox'   => -1,  // hide the search box
                'data-allowclear'  => 'false', // allow to clear a selected value
                'data-placeholder' => /*@translate*/
                    'Distance',
            ]
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
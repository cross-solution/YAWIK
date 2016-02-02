<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Form;

use Jobs\Form\ListFilterAdminFieldset;
use Jobs\Entity\Status;

/**
* @covers \Jobs\Form\ListFilterAdminFieldset
*/
class ListFilterAdminTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $target= new ListFilterAdminFieldset();
        $this->assertInstanceOf('Jobs\Form\ListFilterAdminFieldset', $target);
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Jobs\Form\ListFilterAdminFieldset')
                       ->setMethods(array('add', 'parentInit'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = ['type'       => 'Select',
                 'name'       => 'status',
                 'options'    => array(
                     'value_options' => array(
                         'all' => /*@translate*/ 'All',
                         Status::ACTIVE => /*@translate*/ 'Active',
                         Status::INACTIVE => /*@translate*/ 'Inactive',
                         Status::CREATED => /*@translate*/ 'Created',
                         Status::PUBLISH => /*@translate*/ 'Published',
                         Status::REJECTED => /*@translate*/ 'Rejected',
                         Status::EXPIRED => /*@translate*/ 'Expired',
                     )
                 ),
                 'attributes' => array(
                     'value' => Status::CREATED,
                     'data-searchbox'  => -1,  // hide the search box
                     'data-allowclear' => 'false', // allow to clear a selected value
                 )
        ];

        $add2 = [
            'type' => 'Jobs/ActiveOrganizationSelect',
            'property' => true,
            'name' => 'companyId',
            'options' => array(
                'label' => /*@translate*/ 'Companyname',
            ),
            'attributes' => array(
                'data-placeholder' => /*@translate*/ 'Select hiring organization',
            ),
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
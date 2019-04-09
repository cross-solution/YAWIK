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

use Jobs\Form\ListFilterAdminFieldset;
use Jobs\Entity\Status;

/**
* @covers \Jobs\Form\ListFilterAdminFieldset
*/
class ListFilterAdminTest extends TestCase
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
                         'all' => 'All',
                         Status::ACTIVE => 'Active',
                         Status::INACTIVE => 'Inactive',
                         Status::CREATED => 'Created',
                         Status::PUBLISH => 'Published',
                         Status::REJECTED => 'Rejected',
                         Status::EXPIRED =>  'Expired',
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
                'label' => 'Companyname',
            ),
            'attributes' => array(
                'data-placeholder' => 'Select hiring organization',
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

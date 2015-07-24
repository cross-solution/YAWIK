<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace InstallTest\Form;

use Install\Form\Installation;

/**
 * Tests for \Install\Form\Installation
 *
 * @covers \Install\Form\Installation
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Form
 */
class InstallationTest extends \PHPUnit_Framework_TestCase
{

    public function testExtendsZendForm()
    {
        $this->assertInstanceOf('\Zend\Form\FormInterface', new Installation());
    }

    public function testInit()
    {
        $target = $this->getMockBuilder('\Install\Form\Installation')
                       ->setMethods(array('add', 'setName', 'setAttributes'))
                       ->disableOriginalConstructor()
                       ->getMock();

        $add1 = array(
            'type'       => 'Text',
            'name'       => 'db_conn',
            'options'    => array(
                'label' => 'Database connection string',
            ),
            'attributes' => array(
                'placeholder' => 'mongodb://localhost:27017/YAWIK',
            ),

        );
        $add2 = array(
            'type'    => 'Text',
            'name'    => 'username',
            'options' => array(
                'label' => 'Initial user name',
            ),
        );

        $add3 = array(
            'type'    => 'Password',
            'name'    => 'password',
            'options' => array(
                'label' => 'Password',
            ),
        );

        $target->expects($this->exactly(3))
               ->method('add')
               ->withConsecutive(
                           array($add1),
                           array($add2),
                           array($add3)
               )->will($this->returnSelf());

        $target->expects($this->once())->method('setName')->with('installation');
        $target->expects($this->once())->method('setAttributes')->with(array(
                                                                           'method' => 'post',
                                                                           'action' => '?p=install'
                                                                       )
        );
        /* @var $target \PHPUnit_Framework_MockObject_MockObject|\Install\Form\Installation */
        $target->init();
    }

    public function testGetInputSpecification()
    {
        $expected = array(
            'db_conn'  => array(
                'required'          => true,
                'continue_if_empty' => true,
                'validators'        => array(
                    array('name' => 'Install/ConnectionString'),
                ),
            ),
            'username' => array(
                'required' => true,
            ),
            'password' => array(
                'required' => true
            ),
        );

        $target = new Installation();
        $this->assertEquals($expected, $target->getInputFilterSpecification());
    }
}
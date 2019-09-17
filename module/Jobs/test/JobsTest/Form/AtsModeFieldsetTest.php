<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use Jobs\Entity\AtsMode;
use Jobs\Form\AtsModeFieldset;

/**
 * Tests for AtsModeFieldset
 *
 * @covers \Jobs\Form\AtsModeFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class AtsModeFieldsetTest extends TestCase
{

    /**
     * @testdox Extends \Zend\Form\Fieldset and implements \Core\Form\ViewPartialProviderInterface and \Zend\InputFilter\InputFilterProviderInterface
     */
    public function testExtendsFieldsetAndImplementsRequiredInterfaces()
    {
        $target = new AtsModeFieldset();

        $this->assertInstanceOf('\Zend\Form\Fieldset', $target);
        $this->assertInstanceOf('\Core\Form\ViewPartialProviderInterface', $target);
        $this->assertInstanceOf('\Zend\InputFilter\InputFilterProviderInterface', $target);
    }

    public function testProvidesADefaultViewPartialName()
    {
        $target = new AtsModeFieldset();

        $this->assertEquals('jobs/form/ats-mode', $target->getViewPartial());
    }

    /**
     * @testdox Allows setting and getting view partial name
     */
    public function testSetAndGetViewPartial()
    {
        $target = new AtsModeFieldset();

        $target->setViewPartial('test');

        $this->assertEquals('test', $target->getViewPartial());
    }

    public function testProvidesADefaultHydrator()
    {
        $target = new AtsModeFieldset();

        $this->assertInstanceOf('\Core\Entity\Hydrator\EntityHydrator', $target->getHydrator());
    }

    /**
     * @testdox Allows object binding for entities implementing \Jobs\Entity\AtsModeInterface
     */
    public function testAllowsObjectBindingForEntitiesImplementingAtsModeInterface()
    {
        $target = new AtsModeFieldset();
        $validObject = new AtsMode();
        $invalidObject = new \stdClass();


        $this->assertTrue($target->allowObjectBinding($validObject));
        $this->assertFalse($target->allowObjectBinding($invalidObject));
    }

    /**
     * @testdox Initializes its elements and configuration in init()
     */
    public function testInitialization()
    {
        $target = $this->getMockBuilder('\Jobs\Form\AtsModeFieldset')
                       ->disableOriginalConstructor()
                       ->setMethods(array('add', 'setName'))
                       ->getMock();

        $target->expects($this->once())->method('setName')->with('atsMode');

        $addSelect = array(
            'type' => 'Select',
            'name' => 'mode',
            'options' => array(
                'label' => 'Mode',
                'value_options' => array(
                    'intern' => 'Built-In ATS',
                    'uri'    => 'Use external link',
                    'email'  => 'Get applications via email',
                    'none'   => 'Do not track',
                ),
            ),
            'attributes' => array(
                'data-searchbox' => 'false',
                'value' => 'email',
                'data-width' => '100%'
            )
        );

        $addUri = array(
            'type' => 'Text',
            'name' => 'uri',
            'options' => array(
                'label' => 'URL',
            )
        );

        $addEmail = array(
            'type' => 'Text',
            'name' => 'email',
            'options' => array(
                'label' => 'Email',
            ),
        );
        
        $addOneClickApply = [
            'type' => 'Checkbox',
            'name' => 'oneClickApply',
            'options' => [
                'label' => 'One click apply',
            ]
        ];
        
        $addOneClickApplyProfiles = [
            'type' => 'Select',
            'name' => 'oneClickApplyProfiles',
            'options' => [
                'label' => 'Social profiles',
                'value_options' => [
                    'facebook' => 'Facebook',
                    'xing'     => 'Xing',
                    'linkedin' => 'LinkedIn'
                ],
                'use_hidden_element' => true
            ],
            'attributes' => [
                'multiple' => true,
                'data-width' => '100%'
            ]
        ];

        $target->expects($this->exactly(5))->method('add')
               ->withConsecutive(
                   array($addSelect),
                   array($addUri),
                   array($addEmail),
                   array($addOneClickApply),
                   array($addOneClickApplyProfiles)
                );

        $target->init();
    }

    public function testProvidesInputFilterSpecification()
    {
        $target = new AtsModeFieldset();

        $expected = array('type' => 'Jobs/AtsMode');

        $this->assertEquals($expected, $target->getInputFilterSpecification());
    }
}

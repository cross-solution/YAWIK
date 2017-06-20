<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Form;

use Jobs\Factory\Form\MultipostingSelectFactory;
use Jobs\Options\ChannelOptions;
use Jobs\Options\ProviderOptions;

/**
 * Tests for \Jobs\Factory\Form\MultipostingSelectFactory
 * 
 * @covers \Jobs\Factory\Form\MultipostingSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Form
 */
class MultipostingSelectFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     * @coversNothing
     */
    public function testImplementsFactoryInterface()
    {
        $target = new MultipostingSelectFactory();

        $this->assertInstanceOf('\Zend\ServiceManager\Factory\FactoryInterface', $target);
    }

    /**
     * @testdox Allows creation of a multiposting select element
     */
    public function testCreateService()
    {
        $providerOptions = new ProviderOptions();
        $channelOptions  = new ChannelOptions();

        $channelOptions->setCategory('testCat')
                       ->setCurrency('€')
                       ->setDescription('testDesc')
                       ->setExternalkey('testExtKey')
                       ->setHeadLine('testHL')
                       ->setKey('testKey')
                       ->setLabel('testLabel')
                       ->setLinkText('testLinkTxt')
                       ->setLogo('testLogo')
                       ->setParams(array('test' => 'params'))
                       ->setPrice('test', 1234)
                       ->setPublishDuration(10)
                       ->setRoute('testRoute')
                       ->setTax(19);

        $providerOptions->addChannel($channelOptions);

        $currencyFormat = $this->getMockBuilder('\Zend\I18n\View\Helper\CurrencyFormat')->disableOriginalConstructor()->getMock();
        $currencyFormat->expects($this->any())->method('__invoke')->will($this->returnArgument(0));

        $helpers = $this
	        ->getMockBuilder('\Zend\ServiceManager\AbstractPluginManager')
	        ->disableOriginalConstructor()
	        ->setMethods(array('get'))
			->getMockForAbstractClass()
        ;

        $helpers
	        ->expects($this->once())
	        ->method('get')
	        ->with('currencyFormat')
	        ->willReturn($currencyFormat)
        ;

        $router = $this
	        ->getMockBuilder('\Zend\Mvc\Router\SimpleRouteStack')
	        ->disableOriginalConstructor()
	        ->setMethods(['assemble'])
	        ->getMock()
        ;
        $router
	        ->expects($this->any())
	        ->method('assemble')
	        ->willReturn('/test/uri')
        ;

        $services = $this
	        ->getMockBuilder('\Zend\ServiceManager\ServiceManager')
	        ->disableOriginalConstructor()
	        ->getMock()
        ;

        $servicesMap = array(
            array('Router', $router),
            array('ViewHelperManager', $helpers),
            array('Jobs/Options/Provider', $providerOptions),
        );

        $services->expects($this->exactly(3))
                 ->method('get')
                 ->will($this->returnValueMap($servicesMap))
        ;

        $target = new MultipostingSelectFactory();

        $select = $target->__invoke($services,'irrelevant');

        $this->assertInstanceOf('\Jobs\Form\MultipostingSelect', $select);
        $this->assertEquals('false', $select->getAttribute('data-autoinit'));
        $this->assertEquals('multiple', $select->getAttribute('multiple'));

        $actual = $select->getValueOptions();
        $expected = array(
            'testCat' => array(
                'label' => 'testCat',
                'options' => array(
                    'testKey' => 'testLabel|testHL|testDesc|testLinkTxt|/test/uri|10|testLogo'
                )
            )
        );

        $this->assertEquals($expected, $actual);
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Factory\Form;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Factory\Form\SearchFormFieldsetFactory;
use Cv\Form\SearchFormFieldset;
use Geo\Options\ModuleOptions;
use Zend\ServiceManager\FactoryInterface;

/**
 * Tests for \Cv\Factory\Form\SearchFormFieldsetFactory
 * 
 * @covers \Cv\Factory\Form\SearchFormFieldsetFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Factory
 * @group Cv.Factory.Form
 */
class SearchFormFieldsetFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = SearchFormFieldsetFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateInstance()
    {
        $options = $this
            ->getMockBuilder(ModuleOptions::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPlugin'])
            ->getMock();

        $options->expects($this->once())->method('getPlugin')->willReturn('geoPlugin');

        $services = $this->getServiceManagerMock([
                'Geo/Options' => ['service' => $options, 'count_get' => 1 ],
        ]);

        $plugins = $this->getPluginManagerMock([], $services);

        $fs = $this->target->createService($plugins);

        $this->assertInstanceOf(SearchFormFieldset::class, $fs);
        $this->assertAttributeEquals('geoPlugin', 'locationEngineType', $fs);
    }
    
}
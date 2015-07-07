<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Factory\Repository\Decorator;

use Applications\Factory\Repository\Decorator\HasAppliedFactory;

/**
 * Tests for \Applications\Factory\Repository\Decorator\HasAppliedFactory
 * 
 * @covers \Applications\Factory\Repository\Decorator\HasAppliedFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Repository
 * @group Applications.Factory.Repository.Decorator
 */
class HasAppliedFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Implements \Zend\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\FactoryInterface', new HasAppliedFactory());
    }

    public function testCreatesService()
    {
        $applications = $this->getMockBuilder('\Applications\Repository\Application')
                             ->disableOriginalConstructor()->getMock();


        $repositories = $this->getMockBuilder('\Core\Repository\RepositoryService')
                             ->disableOriginalConstructor()->getMock();

        $repositories->expects($this->once())->method('get')->with('Applications')->willReturn($applications);

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('repositories')->willReturn($repositories);

        $factory = new HasAppliedFactory();

        $object = $factory->createService($services);

        $this->assertInstanceOf('\Applications\Repository\Decorator\HasApplied', $object, 'Wrong type!');
        $this->assertAttributeSame($applications, 'object', $object);
    }
}
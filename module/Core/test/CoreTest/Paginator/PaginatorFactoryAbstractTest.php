<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace CoreTest\Paginator;

use Core\Paginator\PaginatorFactoryAbstract;

/**
 * Class PaginatorFactoryAbstractTest
 *
 * @covers \Core\Paginator\PaginatorFactoryAbstract
 * @package CoreTest\Paginator
 */
class PaginatorFactoryAbstractTest extends \PHPUnit_Framework_TestCase {

    public static function serviceLocator($attribute)
    {

        return strtolower($attribute);
    }

    public function setUp()
    {

    }

    public function testSetGetDefaultTaxRate()
    {
        $staticClassPrefix = '\\' . __CLASS__ . '::';

        if (false) {
            $className = 'Core\Paginator\PaginatorFactoryAbstract';

            $mock = $this->getMockBuilder($className)
                         ->disableOriginalConstructor()
                         ->setMethods(array('createService'))
                         ->getMockForAbstractClass();

            $serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
                ->expect()
                ->method('get')
                ->will($this->returnCallback($this->returnCallback($staticClassPrefix . 'serviceLocator')))
                ->disableOriginalConstructor();

            //$mock->expects($this->once())
            //       ->method('createService')
            //       ->with($serviceLocatorMock);

            //$reflectedClass = new ReflectionClass($className);
            //$constructor = $reflectedClass->getConstructor();
            //$constructor->invoke($mock, 4);
        }

        // Optional: Test anything here, if you want.
        $this->assertTrue(TRUE, 'This should already work.');

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
             'This test has not been implemented yet.'
        );

    }

} 
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

    public function testSetGetDefaultTaxRate()
    {
        //$className = 'Core\Paginator\PaginatorFactoryAbstract';

        //$mock = $this->getMockBuilder($className)
        //             ->disableOriginalConstructor()
        //             ->setMethods(array('createService'))
        //             ->getMockForAbstractClass();

        //$serviceLocatorMock = $this->getMockBuilder('Zend\ServiceManager\ServiceManager')
        //                           ->disableOriginalConstructor();

        //$mock->expects($this->once())
        //       ->method('createService')
        //       ->with($serviceLocatorMock);



        //$reflectedClass = new ReflectionClass($className);
        //$constructor = $reflectedClass->getConstructor();
        //$constructor->invoke($mock, 4);

        // Optional: Test anything here, if you want.
        $this->assertTrue(TRUE, 'This should already work.');

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
             'This test has not been implemented yet.'
        );

    }

} 
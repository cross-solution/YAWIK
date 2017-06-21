<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\Factory\ImageFileCache;

use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Factory\ImageFileCache\ODMListenerFactory;
use Organizations\ImageFileCache\ODMListener;
use Organizations\ImageFileCache\Manager;

/**
 * @coversDefaultClass \Organizations\Factory\ImageFileCache\ODMListenerFactory
 */
class ODMListenerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValueMap([
                ['Organizations\ImageFileCache\Manager', $manager],
            ]));
        
        $factory = new ODMListenerFactory();
        $listener = $factory->__invoke($serviceLocator,'irrelevant');
        $this->assertInstanceOf(ODMListener::class, $listener);
    }
}

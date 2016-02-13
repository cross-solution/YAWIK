<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Service;

use Auth\Factory\Adapter\HybridAuthAdapterFactory;

/**
 * Class HybridAuthAdapterFactoryTest
 * @package AuthTest\Factory\Service
 * @covers \Auth\Factory\Adapter\HybridAuthAdapterFactory
 */
class HybridAuthAdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HybridAuthAdapterFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new HybridAuthAdapterFactory();
    }

    public function testCreateService()
    {

        $hybridAuthMock = $this->getMockBuilder('Hybrid_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Auth/User')
            ->willReturn($userRepositoryMock);

        $sm = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();

        $sm->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('HybridAuth', true, $hybridAuthMock),
                    array('repositories', true, $repositoriesMock)
                )
            ));

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Adapter\HybridAuth', $result);
    }
}
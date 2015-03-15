<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Form\Hydrator;

use Jobs\Factory\Form\Hydrator\OrganizationNameHydratorFactory;
use Test\Bootstrap;

class OrganizationNameHydratorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrganizationNameHydratorFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new OrganizationNameHydratorFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $organizationRepositoryMock = $this->getMockBuilder('Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Organizations/Organization')
            ->willReturn($organizationRepositoryMock);

        $sm->setService('repositories', $repositoriesMock);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Jobs\Form\Hydrator\OrganizationNameHydrator', $result);
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Form\Hydrator\SLFactory;

use Jobs\Form\Hydrator\SLFactory\OrganizationNameHydratorSLFactory;
use Test\Bootstrap;

class OrganizationNameHydratorSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrganizationNameHydratorSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new OrganizationNameHydratorSLFactory();
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
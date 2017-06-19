<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace JobsTest\Factory\Form\Hydrator;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use Jobs\Factory\Form\Hydrator\OrganizationNameHydratorFactory;
use Jobs\Form\Hydrator\Strategy\JobManagerStrategy;
use Jobs\Form\Hydrator\Strategy\OrganizationNameStrategy;


class OrganizationNameHydratorSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    use ServiceManagerMockTrait;

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

        $organizationRepositoryMock = $this->getMockBuilder('Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->getMock();

        $repositories = $this->createPluginManagerMock([
                'Organizations/Organization' => $organizationRepositoryMock,
            ]);

        $container = $this->createServiceManagerMock([
                'repositories' => $repositories,
            ]);


        $result = $this->testedObj->__invoke($container, 'irrelevant');
        $this->assertInstanceOf('Core\Entity\Hydrator\MappingEntityHydrator', $result);
        $this->assertInstanceOf(OrganizationNameStrategy::class, $result->getStrategy('companyId'));
        $this->assertInstanceOf(JobManagerStrategy::class, $result->getStrategy('managers'));
    }
}

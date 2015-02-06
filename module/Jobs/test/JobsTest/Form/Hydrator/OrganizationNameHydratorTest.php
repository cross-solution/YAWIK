<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace JobsTest\Form\Hydrator;

use Jobs\Form\Hydrator\OrganizationNameHydrator;
use JobsTest\Entity\Provider\JobEntityProvider;
use Organizations\Repository\Organization as OrganizationRepo;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class OrganizationNameHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OrganizationNameHydrator
     */
    private $testedObject;

    /**
     * @var MockObject|OrganizationRepo
     */
    private $organizationRepositoryMock;

    public function setUp()
    {
        $this->organizationRepositoryMock = $this->getMockBuilder('Organizations\Repository\Organization')
            ->disableOriginalConstructor()
            ->getMock();

        $this->testedObject = new OrganizationNameHydrator($this->organizationRepositoryMock);
    }

    public function testExtract()
    {
        $job = JobEntityProvider::createEntityWithRandomData(
            array(
                'createOrganization' => array(
                    'createOrganizationName' => true
                )
            )
        );

        $expected = array(
            'company' => $job->getOrganization()->getOrganizationName()->getName(),
            'companyId' => $job->getOrganization()->getId()
        );

        $result = $this->testedObject->extract($job);
        $this->assertSame($expected, $result);
    }
}
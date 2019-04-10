<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\View\Helper;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Job;
use Jobs\View\Helper\JobOrganizationName;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;
use Zend\View\Helper\AbstractHelper;

/**
 * Tests for \Jobs\View\Helper\JobOrganizationName
 *
 * @covers \Jobs\View\Helper\JobOrganizationName
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class JobOrganizationNameTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var string|JobOrganizationName
     */
    private $target = JobOrganizationName::class;

    private $inheritance = [ AbstractHelper::class ];

    public function invokeTestData()
    {
        $job1 = new Job();
        $job1->setCompany('TestCompany');

        $name = new OrganizationName();
        $name->setName('OrgName');

        $org = new Organization();
        $org->setOrganizationName($name);

        $job2 = new Job();
        $job2->setOrganization($org);

        $job3 = new Job();

        $job4 = new Job();
        $job4->setCompany('Override');
        $job4->setOrganization($org);

        return [
            [ $job1, '', 'TestCompany' ],
            [ $job2, '', 'OrgName'],
            [ $job3, 'DefaultName', 'DefaultName' ],
            [ $job4, '', 'Override'],
        ];
    }

    /**
     * @dataProvider invokeTestData
     *
     * @param \Jobs\Entity\JobInterface $job
     * @param string $default
     * @param string $expect
     */
    public function testInvokationReturnsOrganizationName($job, $default, $expect)
    {
        $actual = $this->target->__invoke($job, $default);

        $this->assertEquals($expect, $actual);
    }
}

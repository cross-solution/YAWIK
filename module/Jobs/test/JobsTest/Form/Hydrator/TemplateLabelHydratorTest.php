<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace JobsTest\Form\Hydrator;

use PHPUnit\Framework\TestCase;

use Jobs\Form\Hydrator\TemplateLabelHydrator;
use JobsTest\Entity\Provider\JobEntityProvider;

class TemplateLabelHydratorTest extends TestCase
{
    /**
     * @var TemplateLabelHydrator
     */
    private $testedObject;

    protected function setUp(): void
    {
        $this->testedObject = new TemplateLabelHydrator();
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

        $expected = [
            'description-label-requirements' => $job->getOrganization()->getTemplate()->getLabelRequirements(),
            'description-label-qualifications' => $job->getOrganization()->getTemplate()->getLabelQualifications(),
            'description-label-benefits' => $job->getOrganization()->getTemplate()->getLabelBenefits()
        ];

        $result = $this->testedObject->extract($job);
        $this->assertSame($expected, $result);
    }
}

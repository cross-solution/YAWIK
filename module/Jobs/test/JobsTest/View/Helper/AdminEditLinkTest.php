<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\View\Helper;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Job;
use Jobs\View\Helper\AdminEditLink;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * Tests for \Jobs\View\Helper\AdminEditLink
 *
 * @covers \Jobs\View\Helper\AdminEditLink
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.View
 * @group Jobs.View.Helper
 */
class AdminEditLinkTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        AdminEditLink::class,
        '@testInheritance' => ['as_reflection' => true],
        '@testConstruction' => false,
        'args' => 'getConstructorArgs',
    ];

    private $inheritance = [ AbstractHelper::class ];

    private function getConstructorArgs()
    {
        $helper = $this->getMockBuilder(Url::class)->disableOriginalConstructor()
            ->setMethods(['__invoke'])->getMock();

        $helper->expects($this->once())->method('__invoke')
            ->with(
                'lang/jobs/manage',
                ['action' => 'edit'],
                ['query' => ['id' => 'jobId', 'admin' => 1]],
                true
            )
            ->willReturn('job/edit/link?id=jobId&admin=1');

        return [
            $helper,
            'returnUrl'
        ];
    }


    public function testConstruction()
    {
        $urlHelper = new Url();
        $returnUrl = '/test/a/url?with=parameters';

        $target = new AdminEditLink($urlHelper, $returnUrl);

        $this->assertAttributeSame($urlHelper, 'urlHelper', $target);
        $this->assertAttributeEquals(urlencode($returnUrl), 'returnUrl', $target);
    }

    public function testInvokation()
    {
        $job = new Job();
        $job->setId('jobId');

        $expect = 'job/edit/link?id=jobId&admin=1&return=returnUrl';

        $this->assertEquals($expect, $this->target->__invoke($job));
    }
}

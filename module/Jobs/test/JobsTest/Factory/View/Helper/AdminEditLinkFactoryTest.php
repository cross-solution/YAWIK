<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\View\Helper;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Factory\View\Helper\AdminEditLinkFactory;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Jobs\Factory\View\Helper\AdminEditLinkFactory
 *
 * @covers \Jobs\Factory\View\Helper\AdminEditLinkFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.View
 * @group Jobs.Factory.View.Helper
 */
class AdminEditLinkFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|AdminEditLinkFactory
     */
    private $target = [
        AdminEditLinkFactory::class,
        '@testCreateService' => [
            'mock' => ['__invoke'],
        ]
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testInvoke()
    {
        $request = new Request();
        $query = new Parameters(['test' => 1]);
        $request->setQuery($query);

        $urlHelper = $this->getMockBuilder('\Zend\View\Helper\Url')
            ->disableOriginalConstructor()
            ->setMethods(['__invoke'])
            ->getMock();

        $urlHelper
            ->expects($this->once())
            ->method('__invoke')
            ->with(null, [], ['query' => $query->toArray()], true)
            ->willReturn('returnUrl')
        ;

        $helperManager = $this->getPluginManagerMock(['url' => $urlHelper]);

        $services = $this->createServiceManagerMock([
                'Request' => $request,
                'ViewHelperManager' => $helperManager
            ]);

        $helper = $this->target->__invoke($services, 'irrelevant');

        $this->assertAttributeSame($urlHelper, 'urlHelper', $helper);
        $this->assertAttributeEquals('returnUrl', 'returnUrl', $helper);
    }
}

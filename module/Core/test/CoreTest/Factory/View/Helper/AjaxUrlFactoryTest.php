<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Factory\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\Factory\View\Helper\AjaxUrlFactory;
use Core\View\Helper\AjaxUrl;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Factory\View\Helper\AjaxUrlFactory
 *
 * @covers \Core\Factory\View\Helper\AjaxUrlFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.View
 * @group Core.Factory.View.Helper
 */
class AjaxUrlFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|AjaxUrlFactory
     */
    private $target = [
        AjaxUrlFactory::class,
        '@testCreateService' => [ 'mock' => ['__invoke']],
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $container = $this->getServiceManagerMock();
        $this->target
            ->expects($this->once())
            ->method('__invoke')
            ->with($container, AjaxUrl::class)
        ;
        $this->target->createService($container);
    }

    public function testInvokation()
    {
        $basepath = '/this/is/the/base/path/';
        $request = new Request();
        $request->setBasePath($basepath);

        $container = $this->getServiceManagerMock(['Request' => ['service' => $request, 'count_get' => 1]]);

        $helper = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(AjaxUrl::class, $helper);
        $this->assertAttributeSame($basepath, 'basePath', $helper);
    }
}

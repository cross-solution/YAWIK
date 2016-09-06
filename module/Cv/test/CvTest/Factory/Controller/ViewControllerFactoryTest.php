<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Factory\Controller;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Controller\ViewController;
use Cv\Factory\Controller\ViewControllerFactory;
use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\TranslatorInterface;

/**
 * Tests for \Cv\Factory\Controller\ViewControllerFactory
 *
 * @covers \Cv\Factory\Controller\ViewControllerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
class ViewControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = [
        ViewControllerFactory::class,
        '@testCreateServiceProxiesToInvokeAndPassServiceManager' => VCF_Mock::class,
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function provideCreateServiceTestData()
    {
        return [
            [ 'requestedName' ],
            [ null ],
        ];
    }

    /**
     * @dataProvider provideCreateServiceTestData
     */
    public function testCreateServiceProxiesToInvokeAndPassServiceManager($reqName)
    {
        $services = $this->getServiceManagerMock();
        $plugins  = $this->getPluginManagerMock([], $services, 1);

        $this->target->createService($plugins, null, $reqName);

        $this->assertEquals($reqName ?: ViewController::class, $this->target->reqName);
    }

    public function testInvokeCreatesController()
    {
        $repository = $this->getMockBuilder('\Cv\Repository\Cv')->disableOriginalConstructor()->getMock();
        $repositories = $this->createPluginManagerMock(['Cv/Cv' => [ 'service' => $repository, 'count_get' => 1 ]]);
        $translator = $this->getMockBuilder(TranslatorInterface::class)->getMock();

        $services = $this->getServiceManagerMock([
            'repositories' => [
                'service' => $repositories,
                'count_get' => 1
            ],
            'Translator' => [
                'service' => $translator,
                'count_get' => 1
            ]
        ]);

        $actual = $this->target->__invoke($services, null);

        $this->assertInstanceOf(ViewController::class, $actual);
        $this->assertAttributeSame($repository, 'repository', $actual);
        $repositories->verifyCallCount();
    }
}

/**
 * This Mock is needed because PHPUnit produces incompatible Mock methods.
 * Does not work well with Interop\Container\ContainerInterface
 *
 */
class VCF_Mock extends ViewControllerFactory
{
    public $reqName;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->reqName = $requestedName;
    }
}
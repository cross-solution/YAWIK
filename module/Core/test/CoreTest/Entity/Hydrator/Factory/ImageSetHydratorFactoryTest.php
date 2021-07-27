<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace CoreTest\Entity\Hydrator\Factory;

use Core\Service\FileManager;
use PHPUnit\Framework\TestCase;

use Core\Entity\Hydrator\Factory\ImageSetHydratorFactory;
use Core\Entity\Hydrator\ImageSetHydrator;
use Core\Options\ImageSetOptions;
use Core\Options\ImagineOptions;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Imagine\Image\ImagineInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Entity\Hydrator\Factory\ImageSetHydratorFactory
 *
 * @covers \Core\Entity\Hydrator\Factory\ImageSetHydratorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Hydrator
 * @group Core.Entity.Hydrator.Factory
 */
class ImageSetHydratorFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = [
        ImageSetHydratorFactory::class,
        '@testCreateService' => [
            'mock' => [
                '__invoke' => [
                    '@with' => 'getInvocationArgs',
                    'count' => 1
                ]
            ]
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getInvocationArgs()
    {
        $container   = $this->getServiceManagerMock();
        $controllers = $this->getPluginManagerMock($container);

        return [$container, ImageSetHydrator::class];
    }

    public function testCreateService()
    {
        $container = $this->getServiceManagerMock();
        $pluginManager = $this->getPluginManagerMock();
        $this->target->createService($container, $pluginManager);
    }

    public function testInvokation()
    {
        $imagine = $this->getMockBuilder(ImagineInterface::class)->getMockForAbstractClass();
        $options = new ImageSetOptions();
        $fileManager = $this->createMock(FileManager::class);

        $container = $this->getServiceManagerMock();
        $container->setService('Imagine', $imagine);
        $container->setService(ImageSetOptions::class, $options);
        $container->setService(FileManager::class, $fileManager);

        $hydrator = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(ImageSetHydrator::class, $hydrator);
        $this->assertAttributeSame($imagine, 'imagine', $hydrator);
        $this->assertAttributeSame($options, 'options', $hydrator);
    }
}

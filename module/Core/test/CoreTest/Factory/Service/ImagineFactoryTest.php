<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Service;

use PHPUnit\Framework\TestCase;

use Core\Factory\Service\ImagineFactory;
use Core\Options\ImagineOptions;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Factory\Service\ImagineFactory
 *
 * @covers \Core\Factory\Service\ImagineFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Service
 */
class ImagineFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|ImagineFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ImagineFactory::class,
        '@testCreateService' => ['mock' => ['__invoke' => ['@with' => 'getInvokationMockArgs', 'count' => 1]]],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getInvokationMockArgs()
    {
        return [$this->getServiceManagerMock(), 'Imagine'];
    }
    
    public function invokationTestDataProvider()
    {
        return [
            [ImagineOptions::LIB_GD, \Imagine\Gd\Imagine::class],
            [ImagineOptions::LIB_GMAGICK, \Imagine\Gmagick\Imagine::class],
            [ImagineOptions::LIB_IMAGICK, \Imagine\Imagick\Imagine::class],
            ['invalidLibName', ['\UnexpectedValueException', 'Unsupported image library']],
        ];
    }

    /**
     * @dataProvider invokationTestDataProvider
     *
     * @param $lib
     * @param $expect
     *
     * @return bool
     */
    public function testInvokation($lib, $expect)
    {
        $options = new ImagineOptions(['imageLib' => $lib]);
        $container = $this->getServiceManagerMock([
                ImagineOptions::class => $options
        ]);

        if (is_array($expect)) {
            $this->expectException($expect[0]);
            $this->expectExceptionMessage($expect[1]);
        }

        try {
            $imagine = $this->target->__invoke($container, 'irrelevant');
            $this->assertInstanceOf($expect, $imagine);
            return true;
        } catch (\Imagine\Exception\RuntimeException $e) {
            if (false !== strpos($e->getMessage(), 'not installed')) {
                self::assertTrue(true);
                return true;
            }
        }

        $this->fail('Imagine instance creation for ' .$lib . ' failed.');
    }
}

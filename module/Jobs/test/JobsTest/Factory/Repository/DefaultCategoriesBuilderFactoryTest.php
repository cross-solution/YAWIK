<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Repository;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Category;
use Jobs\Factory\Repository\DefaultCategoriesBuilderFactory;
use org\bovigo\vfs\vfsStream;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Jobs\Factory\Repository\DefaultCategoriesBuilderFactory
 *
 * @covers \Jobs\Factory\Repository\DefaultCategoriesBuilderFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Repository
 */
class DefaultCategoriesBuilderFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var string|DefaultCategoriesBuilderFactory
     */
    private $target = DefaultCategoriesBuilderFactory::class;

    private $inheritance = [FactoryInterface::class];

    public function provideAppConfig()
    {
        return [
            [
                [
                    'config_glob_paths' => ['config/autoload/{,*.}{global,local}.php'],
                    'module_paths' => ['./module', './vendor/'],
                ],
                true,
                ['config/autoload/']
            ],

            [
                [
                    'config_glob_paths' => ['config/autoload/{,*.}{global,local}.php'],
                    'module_paths' => ['./module', './vendor/'],
                ],
                false,
                ['config/autoload/']
            ],
        ];
    }

    /**
     * @dataProvider provideAppConfig
     *
     * @param $config
     * @param $isJobs
     * @param $expectGlobalPaths
     */
    public function testCreateService($config, $isJobs, $expectGlobalPaths)
    {
        vfsStream::setup('/');
        vfsStream::create($isJobs ? ['module' => ['Jobs' => []]] : ['module' => []]);
        $expectModulePath = $isJobs ? vfsStream::url('./module/Jobs/config/') : '.';
        $config['module_paths'] = array_map(function ($i) {
            return vfsStream::url($i);
        }, $config['module_paths']);

        $appConfig['module_listener_options'] = $config;
        $sm = $this->getServiceManagerMock();
        $sm->setService('ApplicationConfig', $appConfig);

        $builder = $this->target->__invoke($sm, 'irrelevant');

        $this->assertAttributeSame($expectModulePath, 'moduleConfigPath', $builder);
        $this->assertAttributeSame($expectGlobalPaths, 'globalConfigPaths', $builder);
        $this->assertAttributeInstanceOf(Category::class, 'categoryPrototype', $builder);
    }
}

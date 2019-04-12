<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Repository;

use PHPUnit\Framework\TestCase;

use Core\Repository\AbstractRepository;
use Core\Repository\DoctrineMongoODM;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Persisters\DocumentPersister;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ODM\MongoDB as ODM;
use Jobs\Repository\Categories;
use Jobs\Repository\DefaultCategoriesBuilder;
use org\bovigo\vfs\vfsStream;

/**
 * Tests for \Jobs\Repository\Categories
 *
 * @covers \Jobs\Repository\Categories
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Repository
 */
class CategoriesTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|Categories
     */
    private $target = [
        Categories::class,
        'args' => false,
        'mock' => ['getService', 'store'],
        '@testInheritance' => [Categories::class, 'as_reflection' => true ],
        '@testFindByCreatesDefaultCategory' => [
            'args' => 'setupTargetArgsForFindTests',
            'mock' => ['createDefaultCategory' => 2],
        ],
    ];

    private $inheritance = [AbstractRepository::class];

    private function setupTargetArgsForFindTests()
    {
        $evm = $this->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dm = $this->getMockBuilder(DocumentManager::class)
                   ->disableOriginalConstructor()
                    ->setMethods(['getEventManager'])
                   ->getMock();

        $dm->expects($this->once())->method('getEventManager')->willReturn($evm);

        $persister = $this->getMockBuilder(DocumentPersister::class)
                          ->disableOriginalConstructor()
                          ->setMethods(['load', 'loadAll'])
                          ->getMock();

        $cursor = $this->getMockBuilder(Cursor::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['toArray'])
                        ->getMock();

        $cursor->expects($this->any())->method('toArray')->willReturn([]);

        $persister->expects($this->any())->method('loadAll')->willReturn($cursor);
        $persister->expects($this->any())->method('load')->willReturn(null);

        $uow = $this->getMockBuilder(UnitOfWork::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['getDocumentPersister'])
                    ->getMock();

        $uow->expects($this->exactly(2))->method('getDocumentPersister')->willReturn($persister);

        $meta = $this->getMockBuilder(ClassMetadata::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $meta->name = 'idonotcare';

        return [$dm, $uow, $meta];
    }

    public function testFindByCreatesDefaultCategory()
    {
        $this->target->findBy([]);
        $this->target->findOneBy([]);
    }

    public function testCreateDefaultCategoryReturnsNull()
    {
        $this->assertNull($this->target->createDefaultCategory('wrongValue'));
    }

    public function provideValueForCreateDefaultCategoryTest()
    {
        return [
            [['value' => 'professions']],
            ['professions'],
            ['employmentTypes']
        ];
    }

    /**
     * @dataProvider provideValueForCreateDefaultCategoryTest
     *
     * @param $value
     */
    public function testCreateDefaultCategory($value)
    {
        $expect = is_array($value) ? $value['value'] : $value;

        $builder = $this->getMockBuilder(DefaultCategoriesBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $builder->expects($this->once())->method('build')->with($expect)->willReturn('success');

        $this->target->expects($this->once())->method('getService')->willReturn($builder);
        $this->target->expects($this->once())->method('store')->with('success');

        $result = $this->target->createDefaultCategory($value);

        $this->assertEquals('success', $result);
    }
}

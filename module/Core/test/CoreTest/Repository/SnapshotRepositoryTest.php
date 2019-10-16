<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Repository;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Entity\EntityInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Repository\DoctrineMongoODM\Event\EventArgs;
use Core\Repository\DoctrineMongoODM\Event\RepositoryEventsSubscriber;
use Core\Repository\SnapshotRepository;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Persisters\DocumentPersister;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Jobs\Entity\Job;
use Jobs\Entity\JobSnapshot;
use Zend\Hydrator\HydratorInterface;

class SnapshotRepositoryTest extends TestCase
{
    use TestSetterGetterTrait;

    /**
     * @var SnapshotRepository
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dm;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $uow;

    protected function setUp(): void
    {
        $this->dm = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->uow = $this->getMockBuilder(UnitOfWork::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->metadata = new ClassMetadata(Job::class);
        $this->target = new SnapshotRepository($this->dm, $this->uow, $this->metadata);
    }

    public function propertiesProvider()
    {
        $hydrator = $this->getMockBuilder(HydratorInterface::class)
            ->getMock();

        return [
            [
                'Hydrator',[
                    '@default'=>EntityHydrator::class,
                    'value'=>$hydrator,
                    'expect_property' => ['hydrator',$hydrator]
                ],
            ],
            [
                'SourceHydrator',[
                    '@default' => EntityHydrator::class,
                    'value' => $hydrator,
                    'expect_property' => ['sourceHydrator',$hydrator]
                ]
            ],
            [
                'SnapshotAttributes',[
                    'default' => [],
                    'value' => ['some'=>'attributes']
                ]
            ]
        ];
    }

    public function testCreate()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $em = $this->getMockBuilder(EventManager::class)
            ->getMock()
        ;
        $this->dm->expects($this->once())
            ->method('getEventManager')
            ->willReturn($em)
        ;

        $cb = function (EventArgs $args) {
            $this->assertInstanceOf(Job::class, $args->get('entity'));
            return true;
        };
        $em->expects($this->once())
            ->method('dispatchEvent')
            ->with(RepositoryEventsSubscriber::postCreate, $this->callback($cb))
        ;
        $this->target->create($entity);
    }

    public function testMerge()
    {
        $job = new Job();
        $job->setTitle('Job Source');
        $snapshot = new JobSnapshot($job);
        $snapshot->setTitle('Job Snapshot');

        /* @var \Jobs\Entity\Job $entity */
        $entity = $this->target->merge($snapshot);
        $this->assertEquals('Job Snapshot', $entity->getTitle());
    }

    public function testDiff()
    {
        $job = new Job();
        $job->setTitle('Some Job Title');
        $snapshot = new JobSnapshot($job);
        $snapshot->setTitle('Some Job Snapshot');

        $this->target->diff($snapshot);
    }

    public function testFindLatest()
    {
        $qb = $this->createMock(Builder::class);
        $query = $this->createMock(Query::class);

        $this->dm->expects($this->once())
            ->method('createQueryBuilder')
            ->with(Job::class)
            ->willReturn($qb)
        ;


        $qb->expects($this->any())
            ->method('field')
            ->willReturn($qb);
        ;
        $qb->expects($this->any())
            ->method('equals')
            ->willReturn($qb);
        ;
        $qb->expects($this->any())
            ->method('sort')
            ->willReturn($qb);
        ;
        $qb->expects($this->any())
            ->method('limit')
            ->willReturn($qb);
        ;
        $qb->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        ;

        $job = new Job();
        $result = new JobSnapshot($job);
        $query->expects($this->any())
            ->method('getSingleResult')
            ->willReturn($result);
        ;

        $em = $this->getMockBuilder(EventManager::class)
            ->getMock()
        ;
        $this->dm->expects($this->once())
            ->method('getEventManager')
            ->willReturn($em)
        ;
        $em->expects($this->once())
            ->method('dispatchEvent')
            ->with(Events::postLoad)
        ;

        $entity = $this->target->findLatest('4af9f23d8ead0e1d32000000');
        $this->assertSame($result, $entity);
    }

    public function testFindBySourceId()
    {
        $persister = $this->createMock(DocumentPersister::class);
        $cursor = $this->createMock(Cursor::class);
        $this->uow->expects($this->once())
            ->method('getDocumentPersister')
            ->willReturn($persister)
        ;

        $sourceId = '4af9f23d8ead0e1d32000000';
        $persister->expects($this->once())
            ->method('loadAll')
            ->with([
                'snapshotEntity' => $sourceId,
                'snapshotMeta.isDraft' => false
            ])
            ->willReturn($cursor)
        ;
        $this->target->findBySourceId($sourceId);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp /Entity must be of type Jobs\\Entity\\Job/
     */
    public function testStoreThrowsOnWrongEntityType()
    {
        $entity = new User();
        $this->target->store($entity);
    }

    public function testStore()
    {
        $entity = new Job();
        $this->dm->expects($this->once())
            ->method('persist')
            ->with($entity)
        ;
        $this->dm->expects($this->once())
            ->method('flush')
            ->with($entity)
        ;
        $this->target->store($entity);
    }

    public function testRemove()
    {
        $entity = new Job();
        $this->dm->expects($this->once())
            ->method('remove')
            ->with($entity)
        ;
        $this->target->remove($entity);
    }
}

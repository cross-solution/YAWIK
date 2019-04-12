<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Repository\DoctrineMongoODM\Event;

use PHPUnit\Framework\TestCase;

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\FileEntity;
use Core\Entity\Permissions;
use Core\Entity\PermissionsAwareInterface;
use Core\Entity\PermissionsAwareTrait;
use Core\Repository\DoctrineMongoODM\Event\AbstractUpdateFilesPermissionsSubscriber;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\UnitOfWork;

/**
 * Tests for \Core\Repository\DoctrineMongoODM\Event\AbstractUpdateFilesPermissionsSubscriber
 *
 * @covers \Core\Repository\DoctrineMongoODM\Event\AbstractUpdateFilesPermissionsSubscriber
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group Core
 * @group Core.Repository
 * @group Core.Repository.DoctrineMongoODM
 * @group Core.Repository.DoctrineMongoODM.Event
 */
class AbstractUpdateFilesPermissionsSubscriberTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|AbstractUpdateFilesPermissionsSubscriber
     */
    private $target = [
        'method' => 'getTarget',
        '@testUpdatesFilesPermissionsOnFlush' => [
            ConcreteUpdateFilesPermissionsSubscriber::class,
            'method' => null,
        ],
    ];

    /** @noinspection PhpUnusedPrivateFieldInspection */
    private $inheritance = [ EventSubscriber::class ];


    private function getTarget()
    {
        return $this
            ->getMockBuilder(AbstractUpdateFilesPermissionsSubscriber::class)
            ->getMockForAbstractClass();
    }

    public function testSubscribesToOnFlushEvent()
    {
        $this->assertEquals([ Events::onFlush ], $this->target->getSubscribedEvents());
    }

    public function testUpdatesFilesPermissionsOnFlush()
    {
        /*
         * Prepare
         */

        $permissions = $this
            ->getMockBuilder(Permissions::class)
            ->setMethods(['hasChanged'])
            ->getMock()
        ;
        $permissions->expects($this->exactly(2))->method('hasChanged')->willReturn(true);

        $document = new Ufps_TargetDocument();
        $document->setPermissions($permissions);

        $filePermissions = $this
            ->getMockBuilder(Permissions::class)
            ->setMethods(['clear', 'inherit'])
            ->getMock();

        $file = new FileEntity();
        $file->setPermissions($filePermissions);

        $collection = new ArrayCollection();
        $collection->add($file);

        $document->singleFile = $file;
        $document->fileCollection = $collection;

        $inserts = [ $document ];
        $updates = [ $document ];

        $filePermissions->expects($this->exactly(4))->method('clear')->will($this->returnSelf());
        $filePermissions->expects($this->exactly(4))->method('inherit')->with($permissions)->will($this->returnSelf());

        $dm = $this
            ->getMockBuilder(DocumentManager::class)
            ->setMethods(['getUnitOfWork', 'getClassMetadata', 'persist'])
            ->disableOriginalConstructor()
            ->getMock();

        $uow = $this
            ->getMockBuilder(UnitOfWork::class)
            ->setMethods(['computeChangeSet', 'getScheduledDocumentInsertions', 'getScheduledDocumentUpdates'])
            ->disableOriginalConstructor()
            ->getMock();

        $args = $this
            ->getMockBuilder(OnFlushEventArgs::class)
            ->setMethods(['getDocumentManager'])
            ->disableOriginalConstructor()
            ->getMock();

        $args->expects($this->once())->method('getDocumentManager')->willReturn($dm);

        $metaData = $this
            ->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dm->expects($this->once())->method('getUnitOfWork')->willReturn($uow);
        $dm->expects($this->exactly(4))->method('getClassMetaData')->with(FileEntity::class)->willReturn($metaData);
        $dm->expects($this->exactly(2))->method('persist')->with($this->identicalTo($filePermissions));

        $uow
            ->expects($this->once())
            ->method('getScheduledDocumentInsertions')
            ->willReturn($inserts);

        $uow
            ->expects($this->once())
            ->method('getScheduledDocumentUpdates')
            ->willReturn($updates);

        $uow->expects($this->exactly(4))->method('computeChangeSet')->with($metaData, $file);

        /*
         * Execute
         */

        $this->target->onFlush($args);
    }
}

class ConcreteUpdateFilesPermissionsSubScriber extends AbstractUpdateFilesPermissionsSubscriber
{
    protected $filesProperties = [ 'singleFile', 'fileCollection' ];
    protected $targetDocument  = Ufps_TargetDocument::class;
}

class Ufps_TargetDocument implements PermissionsAwareInterface
{
    use PermissionsAwareTrait;

    public $singleFile;
    public $fileCollection;

    public function getSingleFile()
    {
        return $this->singleFile;
    }
    public function getFileCollection()
    {
        return $this->fileCollection;
    }
}

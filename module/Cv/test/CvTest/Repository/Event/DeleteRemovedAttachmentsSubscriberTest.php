<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Repository\Event;

use PHPUnit\Framework\TestCase;

use Core\Entity\EntityInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Entity\Attachment;
use Cv\Repository\Event\DeleteRemovedAttachmentsSubscriber;
use Doctrine\Common\EventSubscriber;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

/**
 * Tests for \Cv\Repository\Event\DeleteRemovedAttachmentsSubscriber
 *
 * @covers \Cv\Repository\Event\DeleteRemovedAttachmentsSubscriber
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Repository
 * @group Cv.Repository.Event
 */
class DeleteRemovedAttachmentsSubscriberTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = DeleteRemovedAttachmentsSubscriber::class;

    private $inheritance = [ EventSubscriber::class ];

    private $properties = [
        ['subscribedEvents', ['default' => ['postRemoveEntity']] ]
    ];

    public function testCallbackReturnsNullIfTargetIsNotAnAttachmentEntity()
    {
        $args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDocument'])
            ->getMock()
        ;

        $entity = new \stdClass;

        $args->expects($this->once())->method('getDocument')->willReturn($entity);

        $this->assertNull($this->target->postRemoveEntity($args));
    }

    public function testCallbackPerformsDeleteQuery()
    {
        $fileId = (string) new \MongoId();
        $entity = new Attachment();
        $entity->setId($fileId);

        $q = $this
            ->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $q->expects($this->once())->method('execute');

        $qb = $this
            ->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['update', 'multiple', 'field', 'equals', 'pull', 'getQuery'])
            ->getMock();

        $qb->expects($this->once())->method('update')->will($this->returnSelf());
        $qb->expects($this->once())->method('multiple')->with(true)->will($this->returnSelf());
        $qb->expects($this->once())->method('field')->with('attachments')->will($this->returnSelf());
        $qb->expects($this->once())->method('equals')->with($fileId)->will($this->returnSelf());
        $qb->expects($this->once())->method('pull')->with($fileId)->will($this->returnSelf());
        $qb->expects($this->once())->method('getQuery')->willReturn($q);

        $dm = $this
            ->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])
            ->getMock()
        ;

        $dm->expects($this->once())->method('createQueryBuilder')->with('Cv\Entity\Cv')->willReturn($qb);

        $args = $this
            ->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDocument', 'getDocumentManager'])
            ->getMock()
        ;

        $args->expects($this->once())->method('getDocument')->willReturn($entity);
        $args->expects($this->once())->method('getDocumentManager')->willReturn($dm);

        $this->target->postRemoveEntity($args);
    }
}

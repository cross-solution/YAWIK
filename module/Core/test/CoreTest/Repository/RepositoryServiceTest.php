<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Repository;

use Auth\Entity\User;
use Core\Repository\RepositoryService;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class RepositoryServiceTest extends TestCase
{
    use ServiceManagerMockTrait;

    /**
     * @var RepositoryService
     */
    protected $rs;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $dm;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    protected function setUp(): void
    {
        $this->dm = $this
            ->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventManager = $this->getMockBuilder(EventManager::class)->getMock();
        $this->dm
            ->expects($this->any())
            ->method('getEventManager')
            ->willReturn($this->eventManager);

        $this->rs = new RepositoryService($this->dm);
    }

    /**
     * @dataProvider getTestGetData
     */
    public function testGet($entity, $expected)
    {
        $this->dm
            ->expects($this->once())
            ->method('getRepository')
            ->with($expected);

        $this->rs->get($entity);
    }

    public function getTestGetData()
    {
        return array(
            array('Foo/Bar', '\\Foo\\Entity\\Bar'),
            array('Foo', '\\Foo\\Entity\\Fo')
        );
    }

    public function testCreateQueryBuilder()
    {
        $this->dm
            ->expects($this->once())
            ->method('createQueryBuilder');

        $this->rs->createQueryBuilder();
    }

    public function testStore()
    {
        $user = new User();
        $this->dm
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->dm
            ->expects($this->once())
            ->method('flush')
            ->with($user);

        $this->assertInstanceOf(
            '\Core\Repository\RepositoryService',
            $this->rs->store($user),
            '::store() method should returns $this'
        );
    }

    public function testFlush()
    {
        $user = new User();

        $this->dm
            ->expects($this->once())
            ->method('flush')
            ->with($user);

        $this->eventManager
            ->expects($this->once())
            ->method('hasListeners')
            ->with('postCommit')
            ->willReturn(true);
        $this->eventManager
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with('postCommit');
        $this->rs->flush($user);
    }

    public function testRemove()
    {
        $user = new User();

        $this->dm
            ->expects($this->once())
            ->method('remove')
            ->with($user);

        $this->eventManager
            ->expects($this->once())
            ->method('hasListeners')
            ->with('postRemoveEntity')
            ->willReturn(true);

        $this->eventManager
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with('postRemoveEntity');

        $this->dm
            ->expects($this->never())
            ->method('flush');

        $this->assertInstanceOf(
            '\Core\Repository\RepositoryService',
            $this->rs->remove($user),
            '::remove() method should returns $this'
        );
    }

    public function testRemoveWithFlush()
    {
        $user = new User();

        $this->dm
            ->expects($this->once())
            ->method('remove')
            ->with($user);

        $this->eventManager
            ->expects($this->once())
            ->method('hasListeners')
            ->with('postRemoveEntity')
            ->willReturn(true);

        $this->eventManager
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with('postRemoveEntity');

        $this->dm
            ->expects($this->once())
            ->method('flush');

        $this->assertInstanceOf(
            '\Core\Repository\RepositoryService',
            $this->rs->remove($user, true),
            '::remove() method should returns $this'
        );
    }



    public function testDetach()
    {
        $user = new User();

        $this->dm
            ->expects($this->once())
            ->method('detach')
            ->with($user);

        $this->assertInstanceOf(
            '\Core\Repository\RepositoryService',
            $this->rs->detach($user),
            '::detach() method should returns $this'
        );
    }

    /**
     * @expectedException        \BadMethodCallException
     * @expectedExceptionMessage Method not exists for this class.
     */
    public function testCallDocumentManagerMethod()
    {
        $this->dm
            ->expects($this->once())
            ->method('getEventManager')
            ->willReturn($this->eventManager);

        $this->assertEquals($this->eventManager, $this->rs->getEventManager());

        $this->rs->foo();
    }
}

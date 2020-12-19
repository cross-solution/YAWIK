<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace JobsTest\Auth\Dependency;

use Auth\Dependency\ListItem;
use Auth\Entity\UserInterface;
use Jobs\Repository\Job;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Jobs\Auth\Dependency\ListListener;
use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Laminas\View\Renderer\PhpRenderer as View;
use Jobs\Entity\JobInterface;

/**
 * @coversDefaultClass \Jobs\Auth\Dependency\ListListener
 */
class ListListenerTest extends TestCase
{

    /**
     * @var ListListener
     */
    private $listListener;

    /**
     * @var MockObject|Job
     */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(Job::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->listListener = new ListListener($this->repository);
    }

    /**
     * @covers ::__construct
     */
    public function testInstance()
    {
        $this->assertInstanceOf(\Auth\Dependency\ListInterface::class, $this->listListener);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $this->assertSame($this->listListener, $this->listListener->__invoke());
    }

    /**
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $expected = 'string';
        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        $translator->expects($this->once())
            ->method('translate')
            ->with($this->callback(function ($string) {
                return is_string($string);
            }))
            ->willReturn($expected);

        $this->assertSame($expected, $this->listListener->getTitle($translator));
    }

    /**
     * @covers ::getCount
     */
    public function testGetCount()
    {
        $expected = 3;

        $userId = 'userId';
        $user = $this->createMock(UserInterface::class);
        $user->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $this->repository->expects($this->once())
            ->method('countUserJobs')
            ->with($userId)
            ->willReturn($expected);

        $this->assertSame($expected, $this->listListener->getCount($user));
    }

    /**
     * @covers ::getItems
     */
    public function testGetItems()
    {
        $limit = 10;
        $userId = 'userId';

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $view = $this->getMockBuilder(View::class)
            ->getMock();

        $job = $this->getMockBuilder(JobInterface::class)
            ->getMock();

        $this->repository->expects($this->once())
            ->method('getUserJobs')
            ->with($this->equalTo($userId), $this->equalTo($limit))
            ->willReturn([$job]);

        $actual = $this->listListener->getItems($user, $view, $limit);

        $this->assertIsArray($actual);
        $this->assertCount(1, $actual);
        $this->assertContainsOnlyInstancesOf(ListItem::class, $actual);
    }

    /**
     * @covers ::getEntities
     */
    public function testGetEntities()
    {
        $expected = [];
        $userId = 'userId';

        $user = $this->getMockBuilder(User::class)
            ->getMock();
        $user->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $this->repository->expects($this->once())
            ->method('getUserJobs')
            ->with($this->equalTo($userId))
            ->willReturn($expected);

        $this->assertSame($expected, $this->listListener->getEntities($user));
    }
}

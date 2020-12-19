<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace ApplicationsTest\Auth\Dependency;

use Doctrine\ODM\MongoDB\Query\Builder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Applications\Auth\Dependency\ListListener;
use Applications\Repository\Application as Repository;
use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Auth\Entity\UserInterface as User;
use Laminas\View\Renderer\PhpRenderer as View;
use Applications\Entity\ApplicationInterface;
use Jobs\Entity\JobInterface;

/**
 * @coversDefaultClass \Applications\Auth\Dependency\ListListener
 */
class ListListenerTest extends TestCase
{

    /**
     * @var ListListener
     */
    private $listListener;

    /**
     * @var MockObject
     */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->getMockBuilder(Repository::class)
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

    /*
     * TODO: Test not working due to DoctrineMongoODMModule 3.0 upgrade
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

        $application = $this->getMockBuilder(ApplicationInterface::class)
            ->getMock();
        $application->method('getJob')
            ->willReturn($job);

        $qb = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getQuery'])
            ->getMock()
        ;

        $this->repository->expects($this->once())
            ->method('getUserApplications')
            ->with($this->equalTo($userId), $this->equalTo($limit))
            ->willReturn($qb);

        $actual = $this->listListener->getItems($user, $view, $limit);

        $this->assertIsArray($actual);
        $this->assertCount(1, $actual);
        $this->assertContainsOnlyInstancesOf(\Auth\Dependency\ListItem::class, $actual);
    }
     * /

    /*
     * TODO: Test not working due to DoctrineMongoODMModule 3.0 upgrade
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
            ->method('getUserApplications')
            ->with($this->equalTo($userId))
            ->willReturn($expected);

        $this->assertSame($expected, $this->listListener->getEntities($user));
    }
    */
}

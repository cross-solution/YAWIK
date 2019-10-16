<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Core\Controller\Plugin\Mailer;
use Core\Mail\Message;
use Zend\Mail\Transport\InMemory as InMemoryTransport;
use Core\Mail\MailService;
use Interop\Container\ContainerInterface;

class MailerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mailService;

    /**
     * @var Mailer
     */
    private $target;

    protected function setUp(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->mailService = $this->getMockBuilder(MailService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container->expects($this->any())
            ->method('get')
            ->with('Core/MailService')
            ->willReturn($this->mailService);
        ;
        $this->target = Mailer::factory($container);
    }

    public function testCall()
    {
        $target = $this->target;
        $mailService = $this->mailService;

        $transport = new InMemoryTransport();
        $mailService->expects($this->once())
            ->method('setTransport')
            ->with($transport)
        ;
        $target->setTransport($transport);

        $this->expectException(\BadMethodCallException::class);
        $target->foo();
    }

    public function testSetterAndGetter()
    {
        $this->target->setMailService($this->mailService);
        $this->assertSame(
            $this->mailService,
            $this->target->getMailService()
        );
    }
    public function testGet()
    {
        $this->mailService->expects($this->once())
            ->method('get')
            ->with('some_plugin')
            ->willReturn('returned plugin')
        ;
        $this->assertEquals(
            'returned plugin',
            $this->target->get('some_plugin')
        );
    }

    public function testSend()
    {
        $mail = new Message();
        $this->mailService->expects($this->once())
            ->method('send')
            ->with($mail)
            ->willReturn('returned value')
        ;
        $this->assertEquals(
            'returned value',
            $this->target->send($mail)
        );
    }

    public function testInvokation()
    {
        $mailService = $this->mailService;
        $target = $this->target;
        $mail = new Message();

        // test return to Mailer when $mail is null
        $this->assertSame($target, $target());

        // test invokation send directly
        // if $mail instance of Message class
        $mailService->expects($this->exactly(2))
            ->method('send')
            ->with($mail)
            ->willReturn('returned value')
        ;
        $this->assertEquals('returned value', $target($mail));

        // convert plugin into message
        $mailService->expects($this->exactly(2))
            ->method('get')
            ->with('some plugin', array())
            ->willReturn($mail)
        ;
        $this->assertEquals(
            $mail,
            $target('some plugin', array())
        );
        $this->assertEquals(
            'returned value',
            $target('some plugin', true, true)
        );
    }
}

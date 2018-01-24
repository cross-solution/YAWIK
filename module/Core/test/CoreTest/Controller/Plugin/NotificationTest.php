<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use Core\Controller\Plugin\Notification;
use Core\Listener\Events\NotificationEvent;
use Core\Listener\NotificationListener;
use Core\Log\Notification\NotificationEntity;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * Class NotificationTest
 *
 * @package CoreTest\Controller\Plugin
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30.1
 * @covers \Core\Controller\Plugin\Notification
 */
class NotificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $flashMessenger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $notificationListener;

    /**
     * @var Notification
     */
    private $target;

    public function setUp()
    {
        $manager = $this->createMock(SharedEventManagerInterface::class);
        $this->notificationListener = $this->createMock(NotificationListener::class);
        $this->notificationListener->expects($this->any())
            ->method('getSharedManager')
            ->willReturn($manager)
        ;
        $manager->expects($this->any())
            ->method('attach')
            ->with(
                '*',
                NotificationEvent::EVENT_NOTIFICATION_HTML,
                $this->isType('callable'),
                1
        );

        $this->flashMessenger = $this->createMock(FlashMessenger::class);
        $this->target = new Notification($this->flashMessenger);
        $this->target->setListener($this->notificationListener);
    }

    public function testInvoke()
    {

        $mock = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->setMethods(['addMessage'])
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('addMessage')
            ->with('some message',Notification::NAMESPACE_SUCCESS)
            ->willReturn($mock)
        ;

        /* @var \Core\Controller\Plugin\Notification $mock */
        $this->assertSame(
            $mock,
            $mock(),
            '__invoke Will return self if no parameters passed'
        );
        $this->assertSame(
            $mock,
            $mock('some message',Notification::NAMESPACE_SUCCESS)
        );
    }

    public function testAddMessage()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $listener = $this->notificationListener;
        $target = $this->target;

        $translator->expects($this->once())
            ->method('translate')
            ->willReturnMap([
                ['some message','default','translated some message'],
            ])
        ;
        $listener->expects($this->exactly(2))
            ->method('trigger')
            ->with(NotificationEvent::EVENT_NOTIFICATION_ADD,$this->isInstanceOf(NotificationEvent::class))
        ;

        $target->setTranslator($translator);
        $target->addMessage('some message');

        $entity = new NotificationEntity();
        $entity->setNotification('some entity');
        $entity->setPriority(NotificationEntity::INFO);
        $target->addMessage($entity);
    }

    /**
     * @param string $method
     * @param string $message
     * @dataProvider getTestCreateNamespacedMessages
     */
    public function testCreateNamespacedMessages($method,$message)
    {
        $mock = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->setMethods(['addMessage'])
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('addMessage')
            ->with($message)
            ->willReturn($mock)
        ;

        $callback = array($mock,$method);
        $this->assertSame(
            $mock,
            call_user_func($callback,$message)
        );
    }

    public function getTestCreateNamespacedMessages()
    {
        return [
            ['info','info message'],
            ['warning','warning message'],
            ['success','success message'],
            ['danger','danger message'],
            ['error','error message']
        ];
    }

    public function testCreateOutput()
    {
        $events = $this->createMock(NotificationEvent::class);
        $entity1 = new NotificationEntity();
        $entity1
            ->setNotification('some notification')
            ->setPriority(NotificationEntity::INFO)
        ;
        $entity2 = clone $entity1;
        $entity2->setPriority(NotificationEntity::ALERT);


        $events->expects($this->exactly(2))
            ->method('getTarget')
            ->willReturn($events)
        ;
        $events->expects($this->exactly(2))
            ->method('getNotifications')
            ->willReturnOnConsecutiveCalls(
                [],// setup for empty array test
                [$entity1,$entity2] // setup for non empty array tests
            )
        ;

        $mock = $this->getMockBuilder(Notification::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderMessage'])
            ->getMock()
        ;

        // will nit render message with empty array
        $mock->createOutput($events);


        $mock->expects($this->exactly(2))
            ->method('renderMessage')
            ->withConsecutive(
                ['some notification',Notification::NAMESPACE_INFO],
                ['some notification',Notification::NAMESPACE_DANGER]
            )
        ;
        $mock->createOutput($events);
    }

    public function testRenderMessage()
    {
        $flash = $this->flashMessenger;
        $target = $this->target;

        $flash->expects($this->once())
            ->method('getNamespace')
            ->willReturn('default')
        ;
        $flash->expects($this->exactly(2))
            ->method('setNamespace')
            ->withConsecutive(
                [Notification::NAMESPACE_INFO],
                ['default']
            )
            ->willReturn($flash)
        ;

        $flash->expects($this->once())
            ->method('addMessage')
            ->with('some message')
            ->willReturn($flash)
        ;

        $target->renderMessage('some message',Notification::NAMESPACE_INFO);
    }
}

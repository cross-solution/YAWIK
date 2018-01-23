<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use Core\Controller\Plugin\Mail;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerInterface;
use Zend\Mail\Transport\Sendmail;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Resolver\ResolverInterface;
use Zend\Mail\Transport\InMemory as InMemoryTransport;

class MailControllerTest implements DispatchableInterface
{
    /**
     * @var bool
     */
    private $dispatched;

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $this->dispatched = true;
    }

    public function hasDispatched()
    {
        return $this->dispatched;
    }

}

/**
 * Class MailTest
 *
 * @package CoreTest\Controller\Plugin
 * @covers \Core\Controller\Plugin\Mail
 */
class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mail
     */
    private $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $mailLog;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $viewResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $events;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $controller;

    public function setUp()
    {
        $container = $this->createMock(ContainerInterface::class);
        $this->mailLog = $this->createMock(LoggerInterface::class);
        $this->viewResolver = $this->createMock(ResolverInterface::class);
        $this->moduleManager = $this->createMock(ModuleManagerInterface::class);
        $this->events = $this->createMock(EventManagerInterface::class);
        $this->controller = new MailControllerTest();

        $this->viewResolver->expects($this->any())
            ->method('resolve')
            ->willReturnMap([
                ['mail/null-template',null,null],
                ['coretest/mail/null-template',null,__DIR__.'/fixtures/mail-template.phtml'],
                ['mail/template',null,__DIR__.'/fixtures/mail-template.phtml'],
                ['mail/exception',null,__DIR__.'/fixtures/error.phtml']
            ])
        ;

        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['Log/Core/Mail',$this->mailLog],
                ['ViewResolver',$this->viewResolver],
                ['EventManager',$this->events],
                ['ModuleManager',$this->moduleManager]
            ])
        ;

        $this->target = Mail::factory($container);
        $this->target->setController($this->controller);
    }

    public function testToString()
    {
        $target = $this->target;

        $target->template('null-template');
        $this->assertContains(
            'coretest/mail/null-template',
            $target->__toString()
        );

        $target->template('template');
        $this->assertContains(
            'mail/template',
            $target->__toString()
        );
    }

    public function testTemplate()
    {
        $target = $this->target;
        $params = ['Hello' => 'World','this'=>'this should not rendered'];

        $target($params);
        $target->template('null-template');

        $this->assertInstanceOf(Sendmail::class,$target->getTransport());
        $this->assertSame($this->controller,$target->getController());
        $this->assertContains('Hello World!',$target->getBody());
        $this->assertNotContains($params['this'],$target->getBody());
    }

    public function testTemplateThrowExceptionWhenTemplateFileError()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test Exception');
        $target = $this->target;
        $target->template('exception');
    }

    public function testInformationComplete()
    {
        $target = $this->target;

        $target([
            'Hello' => 'World',
        ]);
        $target->template('template');
        $target->informationComplete();
        $this->assertContains(
            'From Name <from@example.com>',
            $target->__toString()
        );
    }

    public function testInformationCompleteThrowExceptionWhenTemplateNotProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No template provided for Mail.');
        $this->target->informationComplete();
    }

    /**
     * @dataProvider getTestInformationCompleteException
     */
    public function testInformationCompleteException($remove,$message)
    {
        $srcFile = __DIR__.'/fixtures/mail-template.phtml';
        $tplFile = sys_get_temp_dir().'/yawik/mail-template.phtml';

        $contents = file_get_contents($srcFile);
        $pattern = "/^.*".preg_quote($remove).".*$/m";
        $contents = preg_replace($pattern,'',$contents);
        if(!is_dir($dir = dirname($tplFile))){
            mkdir($dir,0777,true);
        }
        file_put_contents($tplFile,$contents,LOCK_EX);

        $resolver = $this->createMock(ResolverInterface::class);
        $resolver->expects($this->any())
            ->method('resolve')
            ->willReturnMap([
                ['mail/exception',null,$tplFile]
            ])
        ;

        $messageRegex = '/^'.$message.'/i';

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('err')
            ->with($this->stringContains($message))
        ;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp($messageRegex);
        $target = new Mail(
            $logger,
            $resolver,
            $this->events,
            $this->moduleManager
        );

        $target->template('exception');
        $target->informationComplete();
    }

    public function getTestInformationCompleteException()
    {
        return [
            ['$from','A from email address'],
            ['$fromName','A from name'],
            ['$subject','A subject must be'],
        ];
    }

    public function testSendThrowException()
    {
        $this->mailLog->expects($this->once())
            ->method('err')
            ->with($this->stringContains('Mail failure'))
        ;
        $target = $this->target;
        $this->assertFalse($target->send());
    }

    public function testSend()
    {
        $transport = new InMemoryTransport();

        $target = $this->target;
        $target([
            'Hello' => 'World'
        ]);
        $target->setTransport($transport);
        $target->getHeaders()->addHeaderLine('to','to@example.com');
        $target->template('template');
        $this->assertTrue($target->send());
        $this->assertContains(
            'Hello World!',
            $transport->getLastMessage()->getBodyText()
        );
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Mail;

use PHPUnit\Framework\TestCase;

use Auth\Options\ModuleOptions as AuthOptions;
use Core\Mail\FileTransport;
use Core\Mail\MailService;
use Core\Mail\MailServiceFactory;
use Core\Options\MailServiceOptions;
use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\TransportInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

/**
 * Class MailServiceFactoryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Mail
 * @covers \Core\Mail\MailServiceFactory
 * @since 0.30.1
 */
class MailServiceFactoryTest extends TestCase
{
    public function testInvokation()
    {
        $container = $this->createMock(ContainerInterface::class);
        $target = $this->getMockBuilder(MailServiceFactory::class)
            ->setMethods(['getTransport'])
            ->getMock()
        ;
        $transport = $this->createMock(TransportInterface::class);

        $authOptions = new AuthOptions();
        $mailOptions = new MailServiceOptions();

        $container->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    ['Config',[]],
                    ['Auth/Options',$authOptions],
                    ['Core/MailServiceOptions',$mailOptions]
            ]
        );


        $target->expects($this->any())
            ->method('getTransport')
            ->willReturn($transport);

        /* @var \Core\Mail\MailService $service */
        /* @var \Zend\ServiceManager\Factory\FactoryInterface $target */
        $service = $target($container, 'some-name');
        $this->assertInstanceOf(
            MailService::class,
            $service
        );
        $this->assertSame($transport, $service->getTransport());
    }

    public function testGetTransport()
    {
        $options = new MailServiceOptions([]);
        $factory = new MailServiceFactory();

        $options->setTransportClass('smtp');
        $this->assertInstanceOf(Smtp::class, $factory->getTransport($options));

        $options->setTransportClass('file');
        $this->assertInstanceOf(FileTransport::class, $factory->getTransport($options));

        $options->setTransportClass('sendmail');
        $this->assertInstanceOf(Sendmail::class, $factory->getTransport($options));

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessageRegExp('/\"foo\" is not a valid/');

        $options->setTransportClass('foo');
        $this->assertInstanceOf(Sendmail::class, $factory->getTransport($options));
    }
}

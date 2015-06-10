<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Mail\MailService;

use Core\Mail\MailService;
use Core\Mail\MailServiceConfig;
use Zend\Mail\Address;
use Zend\Mail\AddressList;
use \Zend\Mail\Message;

/**
 * Tests base behaviour of MailService manager
 *
 * @covers \Core\Mail\MailService
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Mail
 * @group Core.Mail.MailService
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox Extends \Zend\ServiceManager\AbstractPluginmanager
     * @coversNothing
     */
    public function testExtendsAbstractPluginManager()
    {
        $this->assertInstanceOf('\Zend\ServiceManager\AbstractPluginManager', new MailService());
    }

    /**
     * @testdox Defines default mail plugins
     * @coversNothing
     */
    public function testDefaultAttributeValues()
    {
        $target = new MailService();
        $defaultInvokables = array(
            'simple' => '\Zend\Mail\Message',
            'stringtemplate' => '\Core\Mail\StringTemplateMessage',
            'htmltemplate' => '\Core\Mail\HTMLTemplateMessage'
        );

        $this->assertAttributeEquals(false, 'shareByDefault', $target, 'shareByDefault is not set to FALSE');
        $this->assertAttributeEquals($defaultInvokables, 'invokableClasses', $target, 'assert invokableClasses failed.');
        $this->assertAttributeEquals(array(), 'factories', $target, 'factories assertion failed.');
    }

    public function testAddsDefaultInitializersWhenConstructed()
    {
        $config = new MailServiceConfig(array(
            'invokables' => array(
                'testTranslatorMessage' => '\Core\Mail\TranslatorAwareMessage',
                'testMessageWithInit'   => '\CoreTest\Mail\MailService\MessageWithInitMethod',
            )
        ));

        $translator = $this->getMockBuilder('\Zend\I18n\Translator\Translator')
                           ->disableOriginalConstructor()
                           ->getMock();

        $services = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                         ->disableOriginalConstructor()
                         ->getMock();

        $services->expects($this->once())->method('get')->with('translator')->willReturn($translator);

        $target = new MailService($config);
        $target->setServiceLocator($services);

        $mail = $target->get('testTranslatorMessage');

        $this->assertSame($translator, $mail->getTranslator());
        $this->assertTrue($mail->isTranslatorEnabled());

        $mail = $target->get('testMessageWithInit');

        $this->assertTrue($mail->initCalled, 'Init() initializer did not work!');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected instance of \Zend\Mail\Message
     */
    public function testValidatesMailPlugins()
    {
        $target = new MailService();
        $message = new Message();
        $noMessage = new \stdClass();

        $this->assertNull($target->validatePlugin($message));
        $target->validatePlugin($noMessage);

    }

    /**
     * @testdox Allows setting and getting a \Zend\Mail\Transport\TransportInterface
     */
    public function testSetAndGetTransport()
    {
        $target = new MailService();
        $transport = $this->getMockForAbstractClass('\Zend\Mail\Transport\TransportInterface');

        $target->setTransport($transport);

        $this->assertSame($transport, $target->getTransport());
    }

    public function provideFromAddresses()
    {
        $list = new AddressList();
        $list->add('test@email');

        return array(
            array('test@email', null),
            array('test@email', 'TestEmail'),
            array(new Address('test@email'), null),
            array(new Address('test@email', 'UseThisName'), 'testEmail'),
            array($list, 'doNotUse'),
        );
    }

    /**
     * @testdox Allows setting and getting default from address
     * @dataProvider provideFromAddresses
     *
     * @param string|object $email
     * @param string|null $name
     */
    public function testSetAndGetFrom($email, $name)
    {
        $target = new MailService();

        $target->setFrom($email, $name);

        if (is_object($email)) {
            $this->assertSame($email, $target->getFrom());
        } else {
            $expected = null == $name ? $email : array($email => $name);

            $this->assertEquals($expected, $target->getFrom());
        }
    }

    /**
     * @testdox Allows setting and getting X-Mailer header string
     */
    public function testSetAndGetMailer()
    {
        $target = new MailService();
        $expected = 'test/mailer';

        $target->setMailer($expected);

        $this->assertEquals($expected, $target->getMailer());
    }

    /**
     * @testdox Allows setting an override recipients address list
     */
    public function testSetOverrideRecipients()
    {
        $target = new MailService();

        $expected = new AddressList();
        $expected->add('test@email');

        $target->setOverrideRecipient($expected);

        $this->assertAttributeEquals($expected, 'overrideRecipient', $target);
    }

}

class MessageWithInitMethod extends Message
{
    public $initCalled = false;

    public function init()
    {
        $this->initCalled = true;
    }
}

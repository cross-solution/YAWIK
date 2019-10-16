<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Mail\MailService;

use PHPUnit\Framework\TestCase;

use Core\Mail\MailService;
use Core\Mail\Message;
use Zend\Mail\AddressList;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests sending mails via MailService
 *
 * @covers \Core\Mail\MailService
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Mail
 * @group Core.Mail.MailService
 */
class SendMailTest extends TestCase
{
    /**
     * Class under Test
     *
     * @var MailService
     */
    private $target;

    public $expectedMail;

    protected function setUp(): void
    {
        $test = $this;
        $sendCallback = function ($value) use ($test) {
            return $value === $test->expectedMail;
        };
        $transport = $this->getMockForAbstractClass('\Zend\Mail\Transport\TransportInterface');
        $transport->expects($this->once())->method('send')->with($this->callback($sendCallback));

        $target = new MailService(new ServiceManager());
        $target->setTransport($transport);

        $this->target = $target;
    }

    /**
     * @testdox Gets mail service when passed a string to send()
     */
    public function testRetrieveMailBeforeSending()
    {
        $mail = new \Core\Mail\Message();

        $this->target->setService('retrieveme', $mail);
        $this->expectedMail = $mail;

        $this->target->send('retrieveme');
    }

    public function provideFromAddresses()
    {
        return array(
            array(null),
            array('ownfrowm@address'),
        );
    }

    /**
     * @dataProvider provideFromAddresses
     *
     * @param string $from
     */
    public function testSetsDefaultFromAddressOrUsesFromAddressSetInMail($from)
    {
        $mail = new Message();

        $defaultFrom = 'default@from';
        if (null !== $from) {
            $mail->setFrom($from);
            $expectedFrom = $from;
        } else {
            $expectedFrom = $defaultFrom;
        }

        $this->expectedMail = $mail;

        $this->target->setFrom('default@from');
        $this->target->send($mail);

        $mailFrom = $mail->getFrom();
        $this->assertInstanceOf('\Zend\Mail\AddressList', $mailFrom);
        $mailFrom = $mailFrom->get($expectedFrom);
        $this->assertInstanceOf('\Zend\Mail\Address', $mailFrom);
        $mailFrom = $mailFrom->getEmail();
        $this->assertEquals($expectedFrom, $mailFrom);
    }

    /**
     * @testdox recipients gets overidden if override recipients are set
     */
    public function testOverrideRecipient()
    {
        $overrideEmail = 'overidden@email';
        $ccEmail="cc@email";
        $bccEmail="bcc@email";
        $toEmail="to@email";
        
        $recipients = new AddressList();
        $recipients->add($overrideEmail);

        $this->target->setOverrideRecipient($recipients);

        $mail = new Message();
        $mail->addTo($toEmail);
        $mail->addCc($ccEmail);
        $mail->addBcc($bccEmail);

        $this->expectedMail = $mail;

        $this->target->send($mail);

        $headers = $mail->getHeaders();
        $expectedTo = 'To: ' . $overrideEmail;
        $this->assertFalse($headers->has('cc'));
        $this->assertFalse($headers->has('bcc'));
        $this->assertTrue($headers->has('X-Original-Recipients'));

        $this->assertEquals($expectedTo, $headers->get('to')->toString());
        $this->assertEquals('X-Original-Recipients: To: ' . $toEmail . '; Cc: ' . $ccEmail . '; Bcc: ' . $bccEmail, $headers->get('X-Original-Recipients')->toString());
    }

    public function testSetsXMailerHeader()
    {
        $mail = new Message();

        $this->expectedMail = $mail;
        $mailer = 'test/mailer';

        $this->target->setMailer($mailer);
        $this->target->send($mail);

        $headers = $mail->getHeaders();

        $this->assertTrue($headers->has('X-Mailer'));
        $this->assertEquals("X-Mailer: $mailer", $headers->get('X-Mailer')->toString());
    }
}

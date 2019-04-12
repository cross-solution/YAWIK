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

use Auth\Entity\InfoInterface;
use Auth\Entity\UserInterface;
use Zend\Mail\Message as ZendMessage;
use Core\Mail\Message;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Class MessageTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Mail
 * @covers \Core\Mail\Message
 * @since 0.30.1
 */
class MessageTest extends TestCase
{
    use TestInheritanceTrait;

    protected $target = [
        'class' => Message::class
    ];
    protected $inheritance = [ZendMessage::class];

    public function testSetOptions()
    {
        /* @var \Core\Mail\Message $target */

        $options = [
            'from' => 'from@example.com',
            'to' => 'to@example.com'
        ];
        $target = new Message($options);

        $this->assertEquals(
            'from@example.com',
            $target->getFrom()->get('from@example.com')->getEmail()
        );
        $this->assertEquals(
            'to@example.com',
            $target->getTo()->get('to@example.com')->getEmail()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/Expected \$options to be an array/');
        $target->setOptions('foo');
    }

    public function testSetFrom()
    {
        $target = new Message();

        // test with null $emailOrAddressOrList
        $target->setFrom(null);
        $this->assertEquals(0, $target->getFrom()->count());

        // updateAddressList should handle an array with email => display name format
        $target = new Message();
        $target->setFrom(
            ['from@email.com' => 'From Name']
        );
        $this->assertEquals(
            'from@email.com',
            $target->getFrom()->get('from@email.com')->getEmail()
        );
        $this->assertEquals(
            'From Name',
            $target->getFrom()->get('from@email.com')->getName()
        );

        // updateAddressList should handle an array of emails
        $target = new Message();
        $target->setFrom(['int@email.com']);
        $this->assertEquals(
            'int@email.com',
            $target->getFrom()->get('int@email.com')->getEmail()
        );
        $this->assertNull($target->getFrom()->get('int@email.com')->getName());
    }

    public function testSetFromWithUserEntity()
    {
        // test with $emailOrAddressOrList as UserInterface
        $user = $this->createMock(UserInterface::class);
        $info = $this->createMock(InfoInterface::class);
        $user->expects($this->any())
            ->method('getInfo')
            ->willReturn($info)
        ;
        $info->expects($this->any())
            ->method('getEmail')
            ->willReturn('from@email.com')
        ;
        $info->expects($this->any())
            ->method('getDisplayName')
            ->willReturn('From Name')
        ;

        $target = new Message();
        $target->setFrom($user);

        $this->assertEquals(
            'from@email.com',
            $target->getFrom()->get('from@email.com')->getEmail()
        );
        $this->assertEquals(
            'From Name',
            $target->getFrom()->get('from@email.com')->getName()
        );

        $target = new Message();
        $target->setFrom(['from@email.com' => $user]);
        $this->assertEquals(
            'from@email.com',
            $target->getFrom()->get('from@email.com')->getEmail()
        );
        $this->assertEquals(
            'From Name',
            $target->getFrom()->get('from@email.com')->getName()
        );
    }
}

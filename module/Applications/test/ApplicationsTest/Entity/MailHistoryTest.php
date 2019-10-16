<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace ApplicationsTest\Entity;

use PHPUnit\Framework\TestCase;

use Applications\Entity\MailHistory;
use Applications\Entity\Status;

/**
 * Tests for MailHistory
 *
 * @covers \Applications\Entity\MailHistory
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class MailHistoryTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var MailHistory
     */
    private $target;

    protected function setUp(): void
    {
        $status = new Status();
        $this->target = new MailHistory($status, 'message');
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Applications\Entity\HistoryInterface
     * @covers \Applications\Entity\MailHistory::__construct
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\History', $this->target);
        $this->assertInstanceOf('\Applications\Entity\MailHistory', $this->target);
    }

    /**
     * @covers \Applications\Entity\MailHistory::setSubject
     * @covers \Applications\Entity\MailHistory::getSubject
     */
    public function testSetGetSubject()
    {
        $input="subject of the mail";
        $this->target->setSubject($input);
        $this->assertEquals($this->target->getSubject(), $input);
    }

    /**
     * @covers \Applications\Entity\MailHistory::setMailText
     * @covers \Applications\Entity\MailHistory::getMailText
     */
    public function testSetGetMailText()
    {
        $input="this is the mailtext";
        $this->target->setMailText($input);
        $this->assertEquals($this->target->getMailText(), $input);
    }
}

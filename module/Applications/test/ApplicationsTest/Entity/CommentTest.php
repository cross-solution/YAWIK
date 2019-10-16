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

use Applications\Entity\Comment;
use Applications\Entity\Rating;
use Auth\Entity\User;

/**
 * Tests for Comments
 *
 * @covers \Applications\Entity\Comment
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class CommentTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Comment
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Comment();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Auth\Entity\UserInterface
     * @covers \Applications\Entity\Contact::__construct
     */
    public function testExtendsAbstractEntityAndInfo()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\Comment', $this->target);
    }

    public function testSetGetUser()
    {
        $user = new User();
        $user->setId('test');
        $this->target->setUser($user);
        $this->assertEquals($this->target->getUser(), $user);
    }

    public function testSetGetMessage()
    {
        $message="this message";
        $this->target->setMessage($message);
        $this->assertEquals($this->target->getMessage(), $message);
    }

    public function testSetGetDateCreated()
    {
        $date = new \DateTime("2013-01-02");
        $this->target->setDateCreated($date);
        $this->assertEquals($this->target->getDateCreated(), $date);
    }

    public function testSetGetDateModified()
    {
        $date = new \DateTime("2013-01-02");
        $this->target->setDateModified($date);
        $this->assertEquals($this->target->getDateModified(), $date);
    }

    public function testSetGetRating()
    {
        $rating = new Rating();
        $this->target->setRating($rating);
        $this->assertEquals($this->target->getRating(), $rating);
    }

    public function testGetRatingWithoutSetting()
    {
        $this->assertEquals($this->target->getRating(), new Rating());
    }

    public function testPreUpdate()
    {
        $this->target->preUpdate();
        $this->assertInstanceOf("\DateTime", $this->target->getDateModified());
    }

    public function testPrePersist()
    {
        $this->target->prePersist();
        $this->assertInstanceOf("\DateTime", $this->target->getDateCreated());
    }
}

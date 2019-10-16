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

use Applications\Entity\Application;
use Applications\Entity\Attachment;
use Applications\Entity\Attributes;
use Applications\Entity\Comment;
use Applications\Entity\Contact;
use Applications\Entity\Facts;
use Applications\Entity\History;
use Applications\Entity\Rating;
use Applications\Entity\Status;
use Applications\Entity\Subscriber;
use Auth\Entity\Info;
use Auth\Entity\User;
use Core\Entity\Collection\ArrayCollection;
use Applications\Entity\Cv;
use Cv\Entity\Education;
use Jobs\Entity\Job;

/**
 * Tests for Jobs Entity
 *
 * @covers \Applications\Entity\Application
 * @coversDefaultClass \Applications\Entity\Application
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Applications
 * @group  Applications.Entity
 */
class ApplicationTest extends TestCase
{
    /**
     * git dThe "Class under Test"
     *
     * @var Application
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Application();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\ApplicationInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsApplicationInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Applications\Entity\ApplicationInterface', $this->target);
        $this->assertInstanceOf('\Zend\Permissions\Acl\Resource\ResourceInterface', $this->target);
        $this->assertInstanceOf('\Core\Entity\DraftableEntityInterface', $this->target);
    }

    /**
     * @covers \Applications\Entity\Application::getJob
     * @covers \Applications\Entity\Application::setJob
     */
    public function testSetGetJob()
    {
        $user = new User();
        $user->setId(123);
        $job = new Job();
        $job->setUser($user);
        $this->target->setJob($job);
        $this->assertEquals($job, $this->target->getJob());
    }

    /**
     * @covers \Applications\Entity\Application::getContact
     * @covers \Applications\Entity\Application::setContact
     */
    public function testSetGetContactWithContact()
    {
        $contact = new Contact();
        $contact->setEmail('test@test.de')
            ->setLastName('bar')
            ->setFirstName('foo')
            ->setHouseNumber(123)
            ->setStreet('test road')
            ->setCity('frankfurt')
            ->setPostalCode(12345);

        $this->target->setContact($contact);
        $this->assertEquals($contact, $this->target->getContact());
    }

    public function testSetGetContactWithoutContact()
    {
        $this->assertEquals(null, $this->target->getContact());
    }

    public function testSetGetContactWithoutContact2()
    {
        $this->target->setContact(new Info());
        $this->assertEquals(new Contact(), $this->target->getContact());
    }

    /**
     * @covers \Applications\Entity\Application::getFacts
     * @covers \Applications\Entity\Application::setFacts
     */
    public function testSetGetFacts()
    {
        $facts = new Facts();
        $facts->setDrivingLicense(true)
            ->setEarliestStartingDate(new \DateTime())
            ->setExpectedSalary('10000€')
            ->setWillingnessToTravel(false);

        $this->target->setFacts($facts);
        $this->assertEquals($facts, $this->target->getFacts());
    }

    public function testGetFactsWithoutFacts()
    {
        $this->assertEquals(new Facts(), $this->target->getFacts());
    }

    /**
     * @covers \Applications\Entity\Application::getCv
     * @covers \Applications\Entity\Application::setCv
     */
    public function testSetGetCv()
    {
        $cv = new Cv();
        $education= new Education();
        $education->setDescription('test');
        $educations = new ArrayCollection();
        $educations->add($education);

        $cv->setEducations($educations);

        $this->target->setCv($cv);
        $this->assertEquals($cv, $this->target->getCv());
    }

    public function testGetCvWithouCv()
    {
        $this->assertEquals(new Cv(), $this->target->getCv());
    }

    /**
     * @covers \Applications\Entity\Application::getAttachments
     * @covers \Applications\Entity\Application::setAttachments
     */
    public function testSetGetAttachments()
    {
        $attachment = new Attachment();
        $attachment->setName('foo');

        $attachments = new ArrayCollection();
        $attachments->add($attachment);


        $this->target->setAttachments($attachments);
        $this->assertEquals($attachments, $this->target->getAttachments());
    }

    public function testGetAttachmentsWithoutAttachments()
    {
        $this->assertEquals(new ArrayCollection(), $this->target->getAttachments());
    }


    /**
     * @covers \Applications\Entity\Application::setReadBy
     * @covers \Applications\Entity\Application::getReadBy
     */
    public function testSetGetReadBy()
    {
        $readBy = [
            new \MongoId(),
            new \MongoId()
        ];

        $this->target->setReadBy($readBy);
        $this->assertEquals($readBy, $this->target->getReadBy());
    }

    /**
     * @covers \Applications\Entity\Application::isReadBy
     */
    public function testIsReadByWithUser()
    {
        $user = new User();
        $user->setId(123);
        $readBy = [
            123,
            234
        ];
        $this->target->setReadBy($readBy);
        $this->assertEquals(true, $this->target->isReadBy($user));
    }

    /**
     * @covers \Applications\Entity\Application::isReadBy
     */
    public function testIsUnReadByWithUser()
    {
        $user = new User();
        $user->setId(123);
        $readBy = [
            123,
            234
        ];
        $this->target->setReadBy($readBy);
        $this->assertEquals(false, $this->target->isUnReadBy($user));
    }

    /**
     * @covers \Applications\Entity\Application::addReadBy
     */
    public function testAddReadByWithUser()
    {
        $user = new User();
        $user->setId(123);
        $this->target->addReadBy($user);
        $this->assertEquals(false, $this->target->isUnReadBy($user));
        $this->assertEquals(true, $this->target->isReadBy($user));
    }


    /**
     * @covers \Applications\Entity\Application::getComments
     * @covers \Applications\Entity\Application::setComments
     */
    public function testSetGetComments()
    {
        $comment = new Comment();
        $comment->setMessage('test foo bar')
            ->setDateCreated(new \DateTime())
            ->setUser(new User());
        $comments = new ArrayCollection();
        $comments->add($comment);


        $this->target->setComments($comments);
        $this->assertEquals($comments, $this->target->getComments());
    }

    public function testGetCommentsWithoutComments()
    {
        $this->assertEquals(new ArrayCollection(), $this->target->getComments());
    }

    /**
     * @covers \Applications\Entity\Application::getHistory
     * @covers \Applications\Entity\Application::setHistory
     */
    public function testSetGetHistory()
    {
        $history = new History(Status::INCOMING, 'MESSAGE');

        $array = new ArrayCollection();
        $array->add($history);

        $this->target->setHistory($array);
        $this->assertEquals($array, $this->target->getHistory());
    }

    public function testGetEmptyHistory()
    {
        $this->assertEquals(new ArrayCollection(), $this->target->getHistory());
    }

    /**
     * @covers \Applications\Entity\Application::getAttributes
     * @covers \Applications\Entity\Application::setAttributes
     */
    public function testSetGetAttributes()
    {
        $attributes = new Attributes();
        $attributes->setSendCarbonCopy(true)
            ->setAcceptedPrivacyPolicy(true);


        $this->target->setAttributes($attributes);
        $this->assertEquals($attributes, $this->target->getAttributes());
    }

    public function testGetAttributesWithoutAttributes()
    {
        $this->assertEquals(new Attributes(), $this->target->getAttributes());
    }


    /**
     * @testdox Allows setting a the cover letter
     * @covers \Applications\Entity\Application::getSummary
     * @covers \Applications\Entity\Application::setSummary
     */
    public function testSetGetSummary()
    {
        $input = 'Sehr geehrte Damen und Herren';
        $this->target->setSummary($input);
        $this->assertEquals($input, $this->target->getSummary());
    }
    
    /**
     * @testdox Allows setting searchable keywords
     * @covers \Applications\Entity\Application::getKeywords
     * @covers \Applications\Entity\Application::setKeywords
     * @covers \Applications\Entity\Application::clearKeywords
     */
    public function testSetGetKeywords()
    {
        $input = array('Sehr',' geehrte',' Damen' ,'und' ,'Herren');
        $this->target->setKeywords($input);
        $this->assertEquals($input, $this->target->getKeywords());
        $this->target->clearKeywords();
        $this->assertEquals(array(), $this->target->getKeywords());
    }

    
    /**
     * @testdox Allows setting a the cover letter
     * @covers \Applications\Entity\Application::isDraft
     * @covers \Applications\Entity\Application::setIsDraft
     * @dataProvider provideSetGetDraft
     */
    public function testSetGetIsDraft($input, $expected)
    {
        $this->target->setIsDraft($input);
        $this->assertEquals($this->target->isDraft(), $expected);
    }

    public function provideSetGetDraft()
    {
        return [
            [true,true],
            [false,false]
        ];
    }

    public function testGetResourceId()
    {
        $this->assertSame($this->target->getResourceId(), 'Entity/Application');
    }

    public function testSetGetUser()
    {
        $user = new User();
        $this->target->setUser($user);
        $this->assertEquals($this->target->getUser(), $user);
    }

    /**
     * @param $input
     * @param $expected
     * @dataProvider providerSetGetApplicationStatus
     */
    public function testSetGetStatus($input, $expected)
    {
        $this->target->setStatus($input);
        $this->assertEquals($this->target->getStatus(), $expected);
    }

    public function providerSetGetApplicationStatus()
    {
        return [
            [Status::REJECTED,new Status(Status::REJECTED)],
            [Status::INVITED,new Status(Status::INVITED)],
            [Status::INCOMING,new Status(Status::INCOMING)],
            [Status::CONFIRMED,new Status(Status::CONFIRMED)],
            [new Status(Status::CONFIRMED),new Status(Status::CONFIRMED)],
        ];
    }

    public function testSetGetSubscriber()
    {
        $user = new Subscriber();
        $this->target->setSubscriber($user);
        $this->assertEquals($this->target->getSubscriber(), $user);
    }
}

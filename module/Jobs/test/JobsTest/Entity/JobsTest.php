<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Entity;

use Jobs\Entity\Status;
use Jobs\Entity\Job;

/**
 * Tests for Jobs Entity
 *
 * @covers \Jobs\Entity\Job
 * @coversDefaultClass \Jobs\Entity\Job
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class JobsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Job
     */
    private $target;

    public function setup()
    {
        $this->target = new Job();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\AtsModeInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsJobInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\JobInterface', $this->target);
    }

    /**
     * @testdox Allows setting the job title
     * @covers Jobs\Entity\Job::getTitle
     * @covers Jobs\Entity\Job::setTitle
     */
    public function testSetGetTitle()
    {
        $title = 'Software Developer';
        $this->target->setTitle($title);
        $this->assertEquals($title, $this->target->getTitle());
    }

    /**
     * @testdox Allows setting the job language
     * @covers Jobs\Entity\Job::getLanguage
     * @covers Jobs\Entity\Job::setLanguage
     */
    public function testSetGetLanguage()
    {
        $language = 'de';
        $this->target->setLanguage($language);
        $this->assertEquals($language, $this->target->getLanguage());
    }

    /**
     * @testdox Allows setting the job location
     * @covers Jobs\Entity\Job::getLocation
     * @covers Jobs\Entity\Job::setLocation
     */
    public function testSetGetLocation()
    {
        $location = 'Frankfurt am Main';
        $this->target->setLocation($location);
        $this->assertEquals($location, $this->target->getLocation());
    }

    /**
     * @testdox Allows setting the job link
     * @covers Jobs\Entity\Job::getLink
     * @covers Jobs\Entity\Job::setLink
     */
    public function testSetGetLink()
    {
        $link = 'Frankfurt am Main';
        $this->target->setLink($link);
        $this->assertEquals($link, $this->target->getLink());
    }

    /**
     * @testdox Allows setting the job link
     * @covers Jobs\Entity\Job::getPortals
     * @covers Jobs\Entity\Job::setPortals
     */
    public function testSetGetPortals()
    {
        $link = array('jobsintown-de','yawik');
        $this->target->setPortals($link);
        $this->assertEquals($link, $this->target->getPortals());
    }


    /**
     * @testdox Allows setting the application link of a job posting
     * @covers Jobs\Entity\Job::getUriApply
     * @covers Jobs\Entity\Job::setUriApply
     */
    public function testSetGetUriApply()
    {
        $link = 'http://server.de/apply?a=1&b=2';
        $this->target->setUriApply($link);
        $this->assertEquals($link, $this->target->getUriApply());
    }

    /**
     * @testdox Allows to set the publisher URI of a job posting
     * @covers Jobs\Entity\Job::getUriPublisher
     * @covers Jobs\Entity\Job::setUriPublisher
     */
    public function testSetGetUriPublisher()
    {
        $link = 'http://server.de/apply?a=1&b=2';
        $this->target->setUriPublisher($link);
        $this->assertEquals($link, $this->target->getUriPublisher());
    }

    /**
     * @testdox Allows setting the start date of a job posting
     * @covers Jobs\Entity\Job::getDatePublishStart
     * @covers Jobs\Entity\Job::setDatePublishStart
     */
    public function testSetGetDatePublishStart()
    {
        $date = \DateTime::createFromFormat(time(),\DateTime::ISO8601);
        $this->target->setDatePublishStart($date);
        $this->assertEquals($date, $this->target->getDatePublishStart());
    }

    public function provideSetGetStatusTestData()
    {
        return array(
            array("CREATED",        Status::CREATED),
            array(Status::ACTIVE,   Status::ACTIVE),
            array(Status::EXPIRED,  Status::EXPIRED),
            array(Status::PUBLISH,  Status::PUBLISH),
            array(Status::INACTIVE, Status::INACTIVE),
            array(Status::ACTIVE,   Status::ACTIVE),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers Jobs\Entity\Job::getStatus
     * @covers Jobs\Entity\Job::setStatus
     * @dataProvider provideSetGetStatusTestData
     */
    public function testSetGetStatus($status, $expectedStatus)
    {
        $this->target->setStatus($status);
        $this->assertEquals($expectedStatus, $this->target->getStatus());
    }

    /**
     * @testdox Allows setting the reference of a job posting
     * @covers Jobs\Entity\Job::getReference
     * @covers Jobs\Entity\Job::setReference
     */
    public function testSetGetReference()
    {
        $reference = "JD-1234-23";
        $this->target->setReference($reference);
        $this->assertEquals($reference, $this->target->getReference());
    }

    public function provideSetGetAtsEnabledTestData()
    {
        return array(
            array(true,    true),
            array(false,   false),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers Jobs\Entity\Job::getAtsEnabled
     * @covers Jobs\Entity\Job::setAtsEnabled
     * @dataProvider provideSetGetAtsEnabledTestData
     */
    public function testSetGetAtsEnabled($status, $expectedStatus)
    {
        $this->target->setAtsEnabled($status);
        $this->assertEquals($expectedStatus, $this->target->getAtsEnabled());
    }

    public function provideSetGetDraftTestData()
    {
        return array(
            array(true,    true),
            array(false,   false),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers Jobs\Entity\Job::isDraft
     * @covers Jobs\Entity\Job::setIsDraft
     * @dataProvider provideSetGetDraftTestData
     */
    public function testSetGetDraft($status, $expectedStatus)
    {
        $this->target->setIsDraft($status);
        $this->assertEquals($expectedStatus, $this->target->isDraft());
    }

    public function provideSetGetTemplateTestData()
    {
        return array(
            array(null,           'default'),
            array('mytemplate',   'mytemplate'),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers Jobs\Entity\Job::getTemplate
     * @covers Jobs\Entity\Job::setTemplate
     * @dataProvider provideSetGetTemplateTestData
     */
    public function testSetGetTemplate($template, $expectedTemplate)
    {
        $this->target->setTemplate($template);
        $this->assertEquals($expectedTemplate, $this->target->getTemplate());
    }

    public function provideIsActiveTestData()
    {
        return array(
            array(true,  Status::ACTIVE, false),
            array(false, Status::ACTIVE, true),
            array(true,  Status::INACTIVE,false),
            array(false, Status::INACTIVE, false),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers Jobs\Entity\Job::getTemplate
     * @covers Jobs\Entity\Job::setTemplate
     * @dataProvider provideIsActiveTestData
     */
    public function testIsActive($isDraft, $status, $expected)
    {
        $this->target->setIsDraft($isDraft);
        $this->target->setStatus($status);
        $this->assertEquals($expected, $this->target->isActive());
    }


}
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

use PHPUnit\Framework\TestCase;

use Applications\Entity\Application;
use Auth\Entity\Info;
use Auth\Entity\User;
use Core\Entity\AbstractEntity;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\MetaDataProviderInterface;
use Core\Entity\MetaDataProviderTrait;
use CoreTest\Entity\ConcreteEntity;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Jobs\Entity\Classifications;
use Jobs\Entity\JobSnapshot;
use Jobs\Entity\Location;
use Jobs\Entity\Publisher;
use Jobs\Entity\Status;
use Jobs\Entity\AtsMode;
use Jobs\Entity\Job;
use Jobs\Entity\History;
use Jobs\Entity\TemplateValues;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationName;

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
class JobsTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestSetterGetterTrait;

    public function propertiesProvider()
    {
        return [
            ['classifications', ['value' => new Classifications(), 'default@' => Classifications::class]],
        ];
    }
    /**
     * The "Class under Test"
     *
     * @var Job
     */
    private $target = Job::class;

    private $inheritance = [ MetaDataProviderInterface::class ];

    private $traits = [ MetaDataProviderTrait::class ];


    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\AtsModeInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsJobInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\JobInterface', $this->target);
        $this->assertInstanceOf('\Core\Entity\AbstractIdentifiableModificationDateAwareEntity', $this->target);
    }

    /**
     * @testdox Allows setting the job title
     * @covers \Jobs\Entity\Job::getTitle
     * @covers \Jobs\Entity\Job::setTitle
     */
    public function testSetGetTitle()
    {
        $title = 'Software Developer';
        $this->target->setTitle($title);
        $this->assertEquals($title, $this->target->getTitle());
    }

    /**
     * @testdox Allows setting the job language
     * @covers \Jobs\Entity\Job::getLanguage
     * @covers \Jobs\Entity\Job::setLanguage
     */
    public function testSetGetLanguage()
    {
        $language = 'de';
        $this->target->setLanguage($language);
        $this->assertEquals($language, $this->target->getLanguage());
    }

    /**
     * @testdox Allows setting the job location
     * @covers \Jobs\Entity\Job::getLocation
     * @covers \Jobs\Entity\Job::setLocation
     */
    public function testSetGetLocation()
    {
        $location = 'Frankfurt am Main';
        $this->target->setLocation($location);
        $this->assertEquals($location, $this->target->getLocation());
    }

    /**
     * @testdox Allows setting multi job locations
     * @covers \Jobs\Entity\Job::getLocations
     * @covers \Jobs\Entity\Job::setLocations
     */
    public function testSetGetLocations()
    {
        $arrayCollection = new ArrayCollection();
        $location = new Location();
        $location->setCity("Frankfurt");
        $arrayCollection->add($location);
        $this->target->setLocations($arrayCollection);
        $this->assertEquals($arrayCollection, $this->target->getLocations());
    }

    /**
     * @testdox Allows setting/getting multi job applications
     * @covers \Jobs\Entity\Job::getApplications
     * @covers \Jobs\Entity\Job::setApplications
     */
    public function testSetGetApplications()
    {
        $arrayCollection = new ArrayCollection();
        $application = new Application();
        $application->setIsDraft(false);
        $application->setId(123);
        $arrayCollection->add($application);
        $this->target->setApplications($arrayCollection);
        $this->assertEquals($arrayCollection, $this->target->getApplications());
    }

    /**
     * @testdox Allows setting multi job locations
     * @covers \Jobs\Entity\Job::getLocations
     * @covers \Jobs\Entity\Job::setLocations
     */
    public function testGetLocationsWithoutSetting()
    {
        $arrayCollection = new ArrayCollection();
        $this->assertEquals($arrayCollection, $this->target->getLocations());
    }


    /**
     * @testdox Allows setting the job link
     * @covers \Jobs\Entity\Job::getLink
     * @covers \Jobs\Entity\Job::setLink
     */
    public function testSetGetLink()
    {
        $link = 'Frankfurt am Main';
        $this->target->setLink($link);
        $this->assertEquals($link, $this->target->getLink());
    }

    /**
     * @testdox Allows setting the job link
     * @covers \Jobs\Entity\Job::getPortals
     * @covers \Jobs\Entity\Job::setPortals
     */
    public function testSetGetPortals()
    {
        $link = array('jobsintown-de','yawik');
        $this->target->setPortals($link);
        $this->assertEquals($link, $this->target->getPortals());
    }

    /**
     * @testdox Allows setting of a logo reference
     * @covers \Jobs\Entity\Job::getLogoRef
     * @covers \Jobs\Entity\Job::setLogoRef
     */
    public function testSetGetLogoRef()
    {
        $link = 'my/image.jpg';
        $this->target->setLogoRef($link);
        $this->assertEquals($link, $this->target->getLogoRef());
    }

    /**
     * @testdox Allows setting the application link of a job posting
     * @covers \Jobs\Entity\Job::getUriApply
     * @covers \Jobs\Entity\Job::setUriApply
     */
    public function testSetGetUriApply()
    {
        $link = 'http://server.de/apply?a=1&b=2';
        $this->target->setUriApply($link);
        $this->assertEquals($link, $this->target->getUriApply());
    }

    /**
     * @testdox Allows to set the publisher URI of a job posting
     * @covers \Jobs\Entity\Job::getUriPublisher
     * @covers \Jobs\Entity\Job::setUriPublisher
     */
    public function testSetGetUriPublisher()
    {
        $link = 'http://server.de/apply?a=1&b=2';
        $this->target->setUriPublisher($link);
        $this->assertEquals($link, $this->target->getUriPublisher());
    }

    /**
     * @testdox Allows setting the start date of a job posting
     * @covers \Jobs\Entity\Job::getDatePublishStart
     * @covers \Jobs\Entity\Job::setDatePublishStart
     * @dataProvider provideDates
     */
    public function testSetGetDatePublishStart($input, $expected)
    {
        $this->target->setDatePublishStart($input);
        $this->assertEquals($expected, $this->target->getDatePublishStart());
    }

    /**
     * @testdox Allows setting of the end date of a job posting
     * @covers \Jobs\Entity\Job::getDatePublishEnd
     * @covers \Jobs\Entity\Job::setDatePublishEnd
     * @dataProvider provideDates
     */
    public function testSetPublishEnd($input, $expected)
    {
        $this->target->setDatePublishEnd($input);
        $this->assertEquals($expected, $this->target->getDatePublishEnd());
    }

    public function provideDates()
    {
        $date="2011-01-12";
        return [
            [$date, new \DateTime($date)],
            [new \DateTime($date), new \DateTime($date)],
        ];
    }

    public function provideSetGetStatusTestData()
    {
        return array(
            array(Status::CREATED,  Status::CREATED),
            array(Status::ACTIVE,   Status::ACTIVE),
            array(Status::EXPIRED,  Status::EXPIRED),
            array(Status::PUBLISH,  Status::PUBLISH),
            array(Status::INACTIVE, Status::INACTIVE),
            array(Status::ACTIVE,   Status::ACTIVE),
        );
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::getStatus
     * @covers \Jobs\Entity\Job::setStatus
     * @dataProvider provideSetGetStatusTestData
     */
    public function testSetGetStatus($status, $expectedStatus)
    {
        $this->target->setStatus($status);
        $this->assertEquals($expectedStatus, $this->target->getStatus());
    }

    /**
     * @testdox Allows setting the reference of a job posting
     * @covers \Jobs\Entity\Job::getReference
     * @covers \Jobs\Entity\Job::setReference
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
     * @covers \Jobs\Entity\Job::getAtsEnabled
     * @covers \Jobs\Entity\Job::setAtsEnabled
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
     * @covers \Jobs\Entity\Job::isDraft
     * @covers \Jobs\Entity\Job::setIsDraft
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
     * @covers \Jobs\Entity\Job::getTemplate
     * @covers \Jobs\Entity\Job::setTemplate
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
     * @covers \Jobs\Entity\Job::getTemplate
     * @covers \Jobs\Entity\Job::setTemplate
     * @dataProvider provideIsActiveTestData
     */
    public function testIsActive($isDraft, $status, $expected)
    {
        $this->target->setIsDraft($isDraft);
        $this->target->setStatus($status);
        $this->assertEquals($expected, $this->target->isActive());
    }

    public function provideAtsModes()
    {
        return [
            [AtsMode::MODE_EMAIL],
            [AtsMode::MODE_INTERN],
            [AtsMode::MODE_NONE],
            [AtsMode::MODE_URI],
        ];
    }
    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::getAtsMode
     * @covers \Jobs\Entity\Job::setAtsMode
     * @dataProvider provideAtsModes
     */
    public function testSetGetAtsMode($input)
    {
        $atsMode = new AtsMode();
        $atsMode->setMode($input);
        $this->target->setAtsMode($atsMode);
        $this->assertEquals($this->target->getAtsMode(), $atsMode);
    }

    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::getAtsMode
     */
    public function testAtsModeWithoutSetting()
    {
        $this->assertEquals($this->target->getAtsMode(), new AtsMode(AtsMode::MODE_INTERN));
    }

    public function provideTermsAccepted()
    {
        return [
            [true, true],
            [false, false],
            [null, false],
        ];
    }

    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::getTermsAccepted
     * @covers \Jobs\Entity\Job::setTermsAccepted
     * @dataProvider provideTermsAccepted
     */
    public function testSetTermsAccepted($input, $expected)
    {
        $this->target->setTermsAccepted($input);
        $this->assertEquals($this->target->getTermsAccepted(), $expected);
    }

    public function provideHistory()
    {
        $history1 = new \Doctrine\Common\Collections\ArrayCollection([new History(new Status(), 'test')]);

        return [
            [$history1, $history1],
        ];
    }

    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::getHistory
     * @covers \Jobs\Entity\Job::setHistory
     * @dataProvider provideHistory
     */
    public function testSetHistory($input, $expected)
    {
        $this->target->setHistory($input);
        $this->assertEquals($this->target->getHistory(), $expected);
    }

    public function provideStatus()
    {
        return [
            [Status::PUBLISH, Status::PUBLISH],
            [Status::ACTIVE, Status::ACTIVE],
            [Status::CREATED, Status::CREATED],
            [Status::EXPIRED, Status::EXPIRED],
            [Status::REJECTED, Status::REJECTED],
            [Status::INACTIVE, Status::INACTIVE],
        ];
    }


    /**
     * @testdox Allows setting the status of a job posting
     * @covers \Jobs\Entity\Job::changeStatus
     * @dataProvider provideStatus
     */
    public function testChangeStatus($input, $expected)
    {
        $status = new Status($input);
        $msg = "this is the message";
        $this->target->changeStatus($status, $msg);
        $this->assertEquals($this->target->getStatus(), $expected);
    }


    /**
     * @testdox Allows setting the name of a company without organization
     * @covers \Jobs\Entity\Job::setCompany
     * @covers \Jobs\Entity\Job::getCompany
     */
    public function testSetGetCompanyWithoutOrganization()
    {
        $input = "Company ABC";
        $this->target->setCompany($input);
        $this->assertEquals($this->target->getCompany(), $input);
    }

    public function testSetGetCompanyWithOrganization()
    {
        $input1 = "Company ABC";
        $input2 = "Another Company";
        $this->target->setCompany($input1);
        $organization = new Organization();
        $organizationName = new OrganizationName();
        $organizationName->setName($input2);
        $organization->setOrganizationName($organizationName);
        $this->target->setOrganization($organization);
        $this->assertEquals($this->target->getCompany(), $input2);
    }


    /**
     * @testdox Allows setting the name of a company without organization
     * @covers \Jobs\Entity\Job::setApplyId
     * @covers \Jobs\Entity\Job::getApplyId
     */
    public function testSetGetApllyId()
    {
        $input = "MyRerefernce";
        $this->target->setApplyId($input);
        $this->assertEquals($this->target->getApplyId(), $input);
    }

    /**
     * @testdox Allows setting the name of a company without organization
     * @covers \Jobs\Entity\Job::setApplyId
     * @covers \Jobs\Entity\Job::getApplyId
     */
    public function testGetDefaultForApplyId()
    {
        $input = "1234";
        $this->target->setId($input);
        $this->assertEquals($this->target->getApplyId(), $input);
    }

    public function testGetResourceId()
    {
        $this->assertSame($this->target->getResourceId(), 'Entity/Jobs/Job');
    }

    public function testSetGetContactEmail()
    {
        $input = "test@example.com";
        $this->target->setContactEmail($input);
        $this->assertEquals($this->target->getContactEmail(), $input);
    }

    public function testSetGetContactEmailWithUser()
    {
        $input = "test2@example.com";

        $info = new Info();
        $info->setEmail($input);
        $user = new User();
        $user->setInfo($info);
        $this->target->setUser($user);

        $this->assertEquals($this->target->getContactEmail(), $input);
    }

    public function testSetGetTemplateValues()
    {
        $input = new TemplateValues();
        $input->setDescription("Company description");
        $this->target->setTemplateValues($input);
        $this->assertEquals($this->target->getTemplateValues(), $input);
    }

    public function testGetTemplateValuesWithoutSetting()
    {
        $this->assertEquals($this->target->getTemplateValues(), new TemplateValues());
    }

    /**
     * @dataProvider provideTemplatesValuesData
     */
    public function testSetTemplateValues($input, $expected)
    {
        $this->target->setTemplateValues($input);
        $this->assertEquals($this->target->getTemplateValues(), $expected);
    }

    public function provideTemplatesValuesData()
    {
        $templateValues1 = new TemplateValues();
        $templateValues1->setDescription("test");
        $templateValues1->setBenefits("test");

        $templateValues2 = new ConcreteEntityForTemplateValues();
        $templateValues2->setDescription('my description');
        $templateValues2->setTest('invalid');


        return [
            [null, new TemplateValues()],
            [$templateValues2, new TemplateValues($templateValues2)],
            [$templateValues1, $templateValues1]
        ];
    }


    public function testSetGetUserTwice()
    {
        $user1 = new User();
        $user1->setId(123);
        $this->target->setUser($user1);
        $user2 = new User();
        $user2->setId(456);
        $this->target->setUser($user2);
        $this->assertEquals($this->target->getUser(), $user2);
    }

    public function testGetSnapshotGenerator()
    {
        $expected =    array(
            'hydrator' => '',
            'target' => 'Jobs\Entity\JobSnapshot',
            'exclude' => array('permissions', 'history')
        );
        $this->assertSame(
            $this->target->getSnapshotGenerator(),
            $expected
        );
    }

    public function testMakeSnapshot()
    {
        $this->assertEquals($this->target->makeSnapshot(), new JobSnapshot($this->target));
    }

    public function testUnsetUser()
    {
        $user = new User();
        $job = new Job();
        $job->setUser($user);
        $job->unsetUser($user);

        $this->assertNull($job->getUser());
    }

    public function testUnsetOrganization()
    {
        $organization = new Organization();
        $job = new Job();
        $job->setOrganization($organization);

        $job->unsetOrganization(true);

        $this->assertNull($job->getOrganization());
    }
}

class ConcreteEntityForTemplateValues extends AbstractEntity
{
    protected $description;
    protected $test;

    public function setDescription($description)
    {
        $this->description=$description;
        return $this;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setTest($test)
    {
        $this->test=$test;
        return $this;
    }
    public function getTest()
    {
        return $this->testcd;
    }
}

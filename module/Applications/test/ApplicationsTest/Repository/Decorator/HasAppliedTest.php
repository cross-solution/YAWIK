<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Repository\Decorator;

use Applications\Entity\Application;
use Applications\Entity\Contact;
use Applications\Repository\Decorator\HasApplied;
use Auth\Entity\User;
use Jobs\Entity\Job;

/**
 * Tests for \Applications\Repository\Decorator\HasApplied
 * 
 * @covers \Applications\Repository\Decorator\HasApplied
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Repository
 * @group Applications.Repository.Decorator
 */
class HasAppliedTest extends \PHPUnit_Framework_TestCase
{

    private $target;

    public function setUp()
    {
        $this->object = $this->getMockBuilder('\Applications\Repository\Application')->disableOriginalConstructor()->getMock();
        $this->target = new HasApplied($this->object);
    }

    /**
     * @testdox Extends \Core\Decorator\Decorator
     */
    public function testExtendsDecorator()
    {
        $this->assertInstanceOf('\Core\Decorator\Decorator', $this->target);
    }

    public function testHasDefaultObjectType()
    {
        $this->assertAttributeEquals('\Applications\Repository\Application', 'objectType', $this->target);
    }

    public function provideFindByJobAndContactAndUserTestData()
    {

        $contactWoEmail = new Contact();

        $contactWithEmail = new Contact();
        $contactWithEmail->setEmail('some@email');

        $job = new Job();
        $job->setId('jobId');

        $user = new User();
        $user->setId('userId');

        $expected = array(
            'isDraft' => null,
            '$and' => array(
                array('job' => $job->getId()),
                array(
                    '$or' => array(
                        array('user' => $user->getId()),
                        array('permissions.change' => $user->getId()),
                    ),
                ),
            ),
        );

        $expectedWithEmail = $expected;
        $expectedWithEmail['$and'][1]['$or'][] = array('contact.email' => 'some@email');

        return array(
            array($job, $contactWithEmail, $user, $expectedWithEmail),
            array($job, $contactWoEmail, $user, $expected),
        );
    }

    /**
     * @testdox Allows to find applications by informations from the job, the contact and the user.
     * @dataProvider provideFindByJobAndContactAndUserTestData
     */
    public function testFindByJobAndContactAndUser($job, $contact, $user, $expectedQuery)
    {
        $this->object->expects($this->once())->method('findBy')->with($expectedQuery)->willReturn(true);

        $this->target->findByJobAndContactAndUser($job, $contact, $user);
    }

    /**
     * @testdox Allows querying if an user has already applied to an application.
     */
    public function testHasApplied()
    {
        $job = new Job();
        $job->setUser(new User());
        $application = new Application();
        $application->setId('appId');
        $application->setJob($job);
        $application->setContact(new Contact());
        $application->setUser(new User());
        $otherApplication = new Application();
        $otherApplication->setJob($job);
        $otherApplication->setContact(new Contact());
        $otherApplication->setUser(new User());

        $otherApplication->setId('OtherAppId');
        $this->object->expects($this->exactly(4))->method('findBy')->will($this->onConsecutiveCalls(
            array('1', '2', '3'),
            array($application),
            array($application),
            array()
        ));

        $this->assertTrue($this->target->hasApplied($application), 'More than or equal to 2 applications found does not return true.');
        $this->assertFalse($this->target->hasApplied($application), 'Found one application with the same id does not return false.');
        $this->assertTrue($this->target->hasApplied($otherApplication), 'Found one application with different id does not return true');
        $this->assertFalse($this->target->hasApplied($application), 'Found no applications does not return false.');
    }

}
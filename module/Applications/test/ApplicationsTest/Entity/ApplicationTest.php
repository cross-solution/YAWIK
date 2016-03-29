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

use Applications\Entity\Application;
use Applications\Entity\Status;
use Applications\Entity\Subscriber;
use Auth\Entity\User;

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
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * git dThe "Class under Test"
     *
     * @var Application
     */
    private $target;

    public function setup()
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
     * @testdox Allows setting a the cover letter
     * @covers Applications\Entity\Application::getSummary
     * @covers Applications\Entity\Application::setSummary
     */
    public function testSetGetSummary()
    {
        $input = 'Sehr geehrte Damen und Herren';
        $this->target->setSummary($input);
        $this->assertEquals($input, $this->target->getSummary());
    }
    
    /**
     * @testdox Allows setting searchable keywords
     * @covers Applications\Entity\Application::getKeywords
     * @covers Applications\Entity\Application::setKeywords
     * @covers Applications\Entity\Application::clearKeywords
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
     * @covers Applications\Entity\Application::isDraft
     * @covers Applications\Entity\Application::setIsDraft
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

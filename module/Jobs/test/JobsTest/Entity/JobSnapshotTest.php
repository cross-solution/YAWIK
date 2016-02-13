<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace JobsTest\Entity;

use Auth\Entity\User;
use Core\Entity\Permissions;
use Doctrine\Common\Collections\ArrayCollection;
use Jobs\Entity\AtsMode;
use Jobs\Entity\AtsModeInterface;
use Jobs\Entity\JobSnapshot;
use \Jobs\Entity\TemplateValues;
use Organizations\Entity\Organization;

/**
 * Class JobSnapshot
 * @package JobsTest\Entity
 * @covers \Jobs\Entity\JobSnapshot
 * @coversDefaultClass \Jobs\Entity\JobSnapshot
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class JobSnapshotTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var JobSnapshot
     */
    protected $snapShot;

    /**
     *
     */
    public function setup()
    {
        $this->snapShot = new JobSnapshot();
        $this->snapShot->__invoke(array('templateValues' => new TemplateValues()));
    }

    /**
     * @return array
     */
    public function provideTestAttributes()
    {
        return array(
           # ['portals', [1,2,3]], // portals beaks the test. Why???
            ['applyId', 'apply123'],
            ['title', 'title123'],
            ['company', 'company123'],
            ['contactEmail', 'contactEmail123'],
            ['datePublishStart', $date = \DateTime::createFromFormat(time(),\DateTime::ISO8601)],
            ['atsMode', new AtsMode(AtsModeInterface::MODE_EMAIL)],
            ['user', new User()],
            ['organization', new Organization()],
            ['atsEnabled', true],
            ['link', 'http://test/link'],
            ['uriApply', 'http://test/link'],
            ['uriPublisher', 'http://test/link'],
            ['language', 'de'],
            ['location', 'location123'],
            ['locations', 'location123'],
            ['applications', new ArrayCollection()],
            ['status', 'foobar'],
            ['history', new ArrayCollection()],
            ['termsAccepted', 'foobar'],
            ['reference', 'foobar'],
        );
    }

    public function provideTemplateTestAttributes()
    {
        return array(
            ['qualifications', 'qualifications123'],
            ['requirements', 'requirement123'],
            ['benefits', 'benefits123'],
            ['title', 'title123'],
            ['invalidAttribute', 'invalidValue'],
            ['description', 'description of the company']
        );
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideTestAttributes
     * @covers ::__invoke
     *
     * @param string $attribute the mode to set
     * @param string $value
     */
    public function testCopyAttributes($attribute, $value)
    {
        $this->snapShot->__invoke(array($attribute => $value));
        $this->assertEquals($value, $this->snapShot->$attribute);
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideTemplateTestAttributes
     * @covers ::__invoke
     *
     * @param string $attribute the mode to set
     * @param string $value
     */
    public function testCopyTemplateAttributes($attribute, $value)
    {
        $this->snapShot->__invoke(array('templateValues' => array($attribute => $value)));
        $this->assertEquals($value, $this->snapShot->templateValues[$attribute]);
    }

    /**
     * @testdox almost all setters throw an exception, there is only one exception
     * and that is setPermissions
     * @dataProvider provideTestAttributes
     * @expectedException \Core\Exception\ImmutablePropertyException
     */
    public function testImmutableAttributes($attribute, $value)
    {
        $this->snapShot->$attribute = $value;
        $this->assertEquals($value, $this->snapShot->$attribute);
    }

    /**
     * @testdox almost all setters throw an exception, there is only one exception
     * and that is setPermissions
     * @dataProvider provideTestAttributes
     * @expectedException \Core\Exception\ImmutablePropertyException
     */
    public function testImmutableMethods($method, $value)
    {
        $methodName='set' . ucwords($method);
        $this->snapShot->$methodName($value);
        $this->assertEquals($value, $this->snapShot->$value);
    }

    public function testGetResourceId()
    {
        $this->assertEquals($this->snapShot->getResourceId(),null);
    }


    public function testSetGetPermissions()
    {
        $permissions = new Permissions(Permissions::PERMISSION_VIEW);
        $this->snapShot->setPermissions($permissions);
        $this->assertEquals($this->snapShot->permissions,$permissions);
    }

    /**
     *  @expectedException \Core\Exception\ImmutablePropertyException
     */
    public function testSetGetPortals()
    {
        $portals=array(1,2,3);
        $this->snapShot->setPortals($portals);
        $this->assertEquals($this->snapShot->portals,$portals);
    }
}
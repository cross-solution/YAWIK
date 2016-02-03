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
use Jobs\Entity\Job;
use JobsTest\Entity\Provider\JobEntityProvider;

/**
 * Tests for Jobs Entity
 *
 * @covers \Applications\Entity\Application
 * @coversDefaultClass \Applications\Entity\Application
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Entity
 */
class ApplicationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The "Class under Test"
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
     */
    public function testSetGetIsDraft()
    {
        $input = true;
        $this->target->setIsDraft($input);
        $this->assertEquals($input, $this->target->isDraft());
        $input = false;
        $this->target->setIsDraft($input);
        $this->assertEquals($input, $this->target->isDraft());
    }
}
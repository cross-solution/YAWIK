<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace JobsTest\Entity;

use Jobs\Entity\JobSnapshot;
use \Jobs\Entity\TemplateValues;
use Core\Exception\ImmutablePropertyException;

/**
 * Class JobSnapshot
 * @package JobsTest\Entity
 */
class JobSnapshotTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var
     */
    protected $snapShot;

    /**
     * @var
     */
    protected $jobMock;

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
            array('applyId', 'apply123'),
            array('title', 'title123'),
            array('description', 'description123'),
            array('company', 'company123'),
            array('contactEmail', 'contactEmail123'),
            array('location', 'location123'),
            array('datePublishStart', $date = \DateTime::createFromFormat(time(),\DateTime::ISO8601)),
            /* much more */
        );
    }

    public function provideTemplateTestAttributes()
    {
        return array(
            array('qualifications', 'qualifications123'),
            array('requirements', 'requirement123'),
            array('benefits', 'benefits123'),
            array('title', 'title123'),
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
    }
}
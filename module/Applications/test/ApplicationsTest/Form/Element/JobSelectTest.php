<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Form\Element;

use Applications\Form\Element\JobSelect;
use Core\Form\HeadscriptProviderInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Jobs\Entity\Job;
use Zend\Form\Element\Select;

/**
 * Tests for \Applications\Form\Element\JobSelect
 * 
 * @covers \Applications\Form\Element\JobSelect
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Form
 * @group Applications.Form.Element
 */
class JobSelectTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|JobSelect
     */
    private $target = [
        JobSelect::class,
        '@testInitWithoutSelectedJob' => ['mock' => [
            'setAttribute' => ['with' => ['data-element', 'job-select'], 'count' => 1],
            'setValueOptions' => ['with' => [[0 => '']], 'count' => 1],
        ]],
        '@testInitWithSelectedJob' => ['mock' => [
            'setAttribute' => ['count' => 1],
        ]]
    ];

    private $inheritance = [ Select::class, HeadscriptProviderInterface::class ];

    public function propertiesProvider()
    {
        $job = new Job();
        $job->setId('testId');
        $job->setTitle('testTitle');

        return [
            ['headscripts', [
                'default' => ['Applications/js/form.job-select.js'],
                'value' => ['some/scripts/dummy.js']
            ]],
            ['selectedJob', [
                'getter_method' => 'getValue',
                'expect' => $job->getId(),
                'value' => $job,
                'post' => function() use ($job) {
                    $this->assertEquals([0 => '', $job->getId() => $job->getTitle()], $this->target->getValueOptions());
                }
            ]]

        ];
    }

    public function testInitWithoutSelectedJob()
    {
        $this->target->init();
    }

    public function testInitWithSelectedJob()
    {
        $job = new Job();
        $job->setId('a')->setTitle('b');

        $this->target->setSelectedJob($job);

        $this->target->init();

        $this->assertEquals([0 => '', 'a' => 'b'], $this->target->getValueOptions());
    }
}
<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace JobsTest\Form\InputFilter;

use PHPUnit\Framework\TestCase;

use Jobs\Form\InputFilter\JobLocationEdit;

/**
 * Tests for JobLocationEdit
 *
 * @covers \Jobs\Form\InputFilter\JobLocationEdit
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Jobs
 * @group  Jobs.Form
 */

class TestJobLocationEdit extends TestCase
{

    /* @var JobLocationEdit */
    private $inputFilter;

    protected function setUp(): void
    {
        $this->inputFilter = new JobLocationEdit();
        $this->inputFilter->init();
        parent::setUp();
    }

    public function testFormHasTitleAndLocationElements()
    {
        $inputs = $this->inputFilter->getInputs();

        $this->assertArrayHasKey(
            'title',
            $inputs
        );

        $this->assertArrayHasKey(
            'location',
            $inputs
        );
    }

    public function testTrimAndStripTagsFromJobTitle()
    {
        $expected="title (m/w)";
        $output = $this->inputFilter->getInputs();
        $filter = $output['title']->getFilterChain();
        $this->assertEquals($expected, $filter->filter(' <b>title</b> (m/w)'));
    }
}

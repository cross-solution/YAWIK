<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Form;

use Core\Form\SummaryForm;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Form\PreferredJobFieldset;
use Cv\Form\PreferredJobForm;

/**
 * Tests for \Cv\Form\PreferredJobForm
 * 
 * @covers \Cv\Form\PreferredJobForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class PreferredJobFormTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|PreferredJobForm
     */
    private $target = [
        PreferredJobForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setIsDescriptionsEnabled' => ['with' => true, 'count' => 1]],
        ],
    ];

    private $inheritance = [ SummaryForm::class ];

    private $attributes = [
        'baseFieldset' => PreferredJobFieldset::class,
    ];

    public function testInitializesItself()
    {
        $this->target->init();
    }
}
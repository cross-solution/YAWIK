<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use Core\Form\MetaDataFieldset;
use Core\Form\ViewPartialProviderInterface;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Form\CustomerNoteFieldset;

/**
 * Tests for \Jobs\Form\CustomerNoteFieldset
 * 
 * @covers \Jobs\Form\CustomerNoteFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class CustomerNoteFieldsetTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var array|CustomerNoteFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        CustomerNoteFieldset::class,
        '@testInitializationAddsTextarea' => [
            'mock' => ['add' => ['count' => 1, 'with' => [['type' => 'Textarea', 'name' => 'note']]]],
        ],
    ];

    private $inheritance = [ MetaDataFieldset::class, ViewPartialProviderInterface::class ];

    private $attributes = [
        'defaultPartial' => 'jobs/form/customer-note'
    ];

    public function testInitializationAddsTextArea()
    {
        $this->target->init();
    }


}
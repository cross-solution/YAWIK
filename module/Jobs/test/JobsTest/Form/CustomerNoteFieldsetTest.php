<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace JobsTest\Form;

use Laminas\Form\Element\Textarea;
use PHPUnit\Framework\TestCase;

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
class CustomerNoteFieldsetTest extends TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
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
        $target = $this->target;
        $target->init();

        $this->assertEquals('customerNoteFieldset', $target->getAttribute('id'));
        $this->assertEquals('customerNote', $target->getName());

        // check note element
        $elements = $target->getElements();
        $this->assertIsArray($elements);
        $element = $elements['note'];
        $this->assertInstanceOf(Textarea::class, $element);
    }
}

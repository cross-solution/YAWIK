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

use PHPUnit\Framework\TestCase;

use Core\Form\ViewPartialProviderInterface;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Cv\Form\SearchForm;

/**
 * Tests for \Cv\Form\SearchForm
 *
 * @covers \Cv\Form\SearchForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Test
 */
class SearchFormTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|SearchForm|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        SearchForm::class,
        '@testInitializesItself' => [
            'mock' => [
                'setName',
                'setAttributes' => ['with' => [['id' => 'cv-list-filter']], 'count' => 1],
                'setOption',
                'add',
                'addTextElement' => ['count' => 1],
                'setButtonElement' => ['with' => ['d'], 'count' => 1],
                'addButton' => ['return' => true],
            ],
        ],
    ];

    private $inheritance = [ \Core\Form\SearchForm::class ];

    public function testInitializesItself()
    {
        $this->target->expects($this->exactly(3))->method('setOption')
            ->withConsecutive(
                ['text_name', 'search'],
                ['text_placeholder', 'Search for resumes'],
                ['text_span', 5]
            );

        $this->target->expects($this->exactly(2))->method('setName')
            ->withConsecutive(
                [$this->anything()],
                ['cv-list-filter']
            );
        $add1 = [
                 'name' => 'l',
                 'type' => 'LocationSelect',
                 'options' => [
                     'label' => 'Location',
                     'span' => 3,
                     'location_entity' => \Cv\Entity\Location::class,
                 ],
                 'attributes' => [
                     'data-width' => '100%',
                 ],
             ];

        $add2 = [
                 'name' => 'd',
                 'type' => 'Core\Form\Element\Select',
                 'options' => [
                     'label' => /*@translate*/ 'Distance',
                     'value_options' => [
                         '5' => '5 km',
                         '10' => '10 km',
                         '20' => '20 km',
                         '50' => '50 km',
                         '100' => '100 km'
                     ],
                     'span' => 4,

                 ],
                 'attributes' => [
                     'value' => '10',
                     'data-searchbox' => -1,
                     'data-allowclear' => 'false',
                     'data-placeholder' => 'Distance',
                     'data-width' => '100%',
                 ]
             ];

        $this->target->expects($this->exactly(2))->method('add')
            ->withConsecutive(
                [$add1],
                [$add2]
            );

        $this->target->init();
    }
}

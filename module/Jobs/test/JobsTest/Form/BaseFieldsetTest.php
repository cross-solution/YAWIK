<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Form;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Location;
use Jobs\Form\BaseFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Jobs\Form\BaseFieldset
 *
 * @covers \Jobs\Form\BaseFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Form
 */
class BaseFieldsetTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        BaseFieldset::class,
        '@testInitialize' => [
            'mock' => ['setAttribute' => ['with' => ['id', 'job-fieldset']], 'setName' => ['with' => 'jobBase'], 'add'],
        ],
    ];

    private $inheritance = [ Fieldset::class ];

    public function testInitialize()
    {
        $this->target->expects($this->exactly(2))->method('add')
            ->withConsecutive(
                [
                    [
                        'type' => 'Text',
                        'name' => 'title',
                        'options' => [
                            'label' => /*@translate*/ 'Job title',
                            'description' => /*@translate*/ 'Please enter the job title'
                        ],
                    ]
                ],
                [
                    [
                        'type' => 'LocationSelect',
                        'name' => 'geoLocation',
                        'options' => [
                            'label' => /*@translate*/ 'Location',
                            'description' => /*@translate*/ 'Please enter the location of the job',
                            'location_entity' => Location::class,
                            'summary_value' => [$this->target, 'getLocationsSummaryValue'],
                        ],
                        'attributes' => [
                            'data-width' => '100%'
                        ]
                    ]
                ]
            );

        $this->target->init();
    }
}

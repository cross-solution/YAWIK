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

use Core\Entity\Hydrator\EntityHydrator;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Entity\Language;
use Cv\Form\LanguageSkillFieldset;
use Zend\Form\Fieldset;

/**
 * Tests for \Cv\Form\LanguageSkillFieldset
 *
 * @covers \Cv\Form\LanguageSkillFieldset
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Form
 */
class LanguageSkillFieldsetTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|LanguageSkillFieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        LanguageSkillFieldset::class,
        '@testInitializesItself' => [
            'mock' => [
                'add',
                'setName' => ['with' => 'language', 'count' => 1, 'return' => '__self__'],
                'setHydrator' => ['@with' => ['isInstanceOf', EntityHydrator::class ], 'count' => 1, 'return' => '__self__'],
                'setObject' => ['@with' => ['isInstanceOf', Language::class ], 'count' => 1, 'return' => '__self__'],
                'setLabel' => ['with' => 'Language', 'count' => 1]
            ],
            'args' => false,
        ],
    ];

    private $inheritance = [ Fieldset::class ];

    public function testInitializesItself()
    {
        $add = [
            [
                'name' => 'language',
                'type' => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-autoinit' => 'false',
                ],
            ],
            [
                'name' => 'levelListening',
                'type' => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-allowclear'  => 'true',
                    'data-searchbox'   => -1,
                ]
            ],
            [
                'name' => 'levelReading',
                'type' => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-allowclear'  => 'true',
                    'data-searchbox'   => -1,
                    'data-autoinit'    => "false"
                ]
            ],
            [
                'name'       => 'levelSpokenInteraction',
                'type'       => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-allowclear' => 'true',
                    'data-searchbox'  => -1,
                ]
            ],
            [
                'name'       => 'levelSpokenProduction',
                'type'       => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-allowclear' => 'true',
                    'data-searchbox'  => -1,
                ]
            ],
            [
                'name'       => 'levelWriting',
                'type'       => 'Core\Form\Element\Select',
                'attributes' => [
                    'data-allowclear' => 'true',
                    'data-searchbox'  => -1,
                ]
            ],
        ];

        $callCount = count($add);

        $arrayContainsPairs = function ($expected, $actual) use (&$arrayContainsPairs) {
            foreach ($expected as $key => $value) {
                if (!array_key_exists($key, $actual)) {
                    return false;
                }

                if (is_array($value)) {
                    return $arrayContainsPairs($value, $actual[$key]);
                }

                return $value === $actual[$key];
            }
        };


        $addArgValidator = function ($arg) use ($add, $callCount, $arrayContainsPairs) {
            static $count = 0;

            /* PPHUnit calls this callback again after all invokations are made
             * I don't know why, but therefor the need to check if $count is greater that 7
             */
            return $callCount < $count + 1 || $arrayContainsPairs($add[$count++], $arg);
        };

        $this->target
            ->expects($this->exactly($callCount))
            ->method('add')
            ->with($this->callback($addArgValidator))
            ->will($this->returnSelf())
        ;

        $this->target->init();
    }
}

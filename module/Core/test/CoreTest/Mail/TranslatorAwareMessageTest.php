<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Mail;

use PHPUnit\Framework\TestCase;

use Core\Mail\TranslatorAwareMessage;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\Mail\Message;

/**
 * Tests for \Core\Mail\TranslatorAwareMessage
 *
 * @covers \Core\Mail\TranslatorAwareMessage
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Mail
 */
class TranslatorAwareMessageTest extends TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|TranslatorAwareMessage
     */
    private $target = TranslatorAwareMessage::class;

    private $inheritance = [ Message::class, TranslatorAwareInterface::class ];

    public function propertiesProvider()
    {
        $setTranslator = function () {
            $this->target->setTranslator(new Translator());
        };

        return [
            'translator' => ['translator', new Translator()],

            'textDomain' => ['translatorTextDomain', ['default' => 'default', 'value' => 'another']],

            'enabledWithoutTranslator' =>
            [
                'translatorEnabled', [
                    'ignore_setter' => true,
                    'getter_method' => 'is*',
                    'value' => false,
                ]
            ],

            'enabledWithTranslator' =>
            [
                'translatorEnabled', [
                    'getter_method' => 'is*',
                    'pre' => $setTranslator,
                    'value' => true,
                ]
            ],

            'hasTranslatorWithoutTranslator' =>
                ['translator', ['getter_method' => 'has*', 'ignore_setter' => true, 'value' => false]],

            'hasTranslatorWithTranslator' =>
                ['translator', ['getter_method' => 'has*', 'ignore_setter' => true, 'value' => true, 'pre' => $setTranslator]],

            'subject' => ['subject', ['value' => 'untranslated', 'setter_args' => [false]]],
        ];
    }


    private function injectTranslator($subject, $domain = 'default', $translatedSubject = null)
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()
            ->setMethods(['translate'])->getMock();

        $translator->expects($this->once())->method('translate')
            ->with($subject, $domain)->will($this->returnValue($translatedSubject ?: $subject));

        $this->target->setTranslator($translator);
        $this->target->setTranslatorTextDomain($domain);
    }

    /**
     * @covers \Core\Mail\TranslatorAwareMessage::setSubject()
     */
    public function testSetSubjectDoesNotTranslate()
    {
        $translator = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()
                           ->setMethods(['translate'])->getMock();
        $translator->expects($this->never())->method('translate');

        $this->target->setTranslator($translator);

        $this->target->setSubject('notranslate', false);
    }

    /**
     * @covers \Core\Mail\TranslatorAwareMessage::setSubject()
     */
    public function testSubjectTranslatesSimpleStrings()
    {
        $this->injectTranslator('simplestring');

        $this->target->setSubject('simplestring');
    }

    /**
     * @covers \Core\Mail\TranslatorAwareMessage::setSubject()
     */
    public function testSetSubjectTranslatesStringsWithPlaceholders()
    {
        $this->injectTranslator('string with %s', 'another');

        $this->target->setSubject('string with %s', 'placeholder');

        $this->assertEquals('string with placeholder', $this->target->getSubject());
    }
}

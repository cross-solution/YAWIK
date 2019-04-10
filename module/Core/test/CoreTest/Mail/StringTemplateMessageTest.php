<?php

/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Mail;

use PHPUnit\Framework\TestCase;

use Core\Mail\StringTemplateMessage;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mail\AddressList;
use Zend\Stdlib\ArrayUtils;

/**
 * Class StringTemplateMessageTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Mail
 * @covers \Core\Mail\StringTemplateMessage
 * @since 0.30.1
 */
class StringTemplateMessageTest extends TestCase
{
    use TestSetterGetterTrait, SetupTargetTrait;

    protected $target = [
        'class' => StringTemplateMessage::class
    ];

    /**
     * Test constructor define default content type
     */
    public function testConstruct()
    {
        $target = new StringTemplateMessage();
        $headers = $target->getHeaders()->toString();
        $this->assertContains('Content-Type: text/plain;', $headers);
        $this->assertContains('charset="UTF-8"', $headers);
        $this->assertEquals('UTF-8', $target->getEncoding());
    }

    public function testVariables()
    {
        $variables = ['some'=>'value'];
        $target = new StringTemplateMessage();
        $this->assertTrue(is_array($target->getVariables()));
        $target->setVariable('foo', 'bar');
        $target->setVariables($variables);
        $this->assertEquals($variables, $target->getVariables());
    }

    public function someCallback()
    {
        return 'Hello World';
    }

    public function propertiesProvider()
    {
        $addressList = new AddressList();
        $addressValue = ArrayUtils::iteratorToArray($addressList);
        return [
            ['variables',[
                'value' => ['some' => 'value'],
                'default' => [],
            ]],
            ['variables',[
                'value' => $this,
                'setter_exception' => [
                    \InvalidArgumentException::class,
                    'Expect an array or an instance of \Traversable'
                ],

            ]],
            ['variables',[
                'value' => $addressList,
                'expect' => $addressValue
            ]],
            ['template',[
                'value' => 'some-template',
                'default' => null
            ]],
            ['callbacks',[
                'value' => [
                    'some' => [$this,'someCallback']
                ],
                'default' => array(),
            ]],
            ['callbacks',[
                'value' => $this,
                'setter_exception' => [
                    \InvalidArgumentException::class,
                    'Expect an array or an instance of \Traversable, but received'
                ]
            ]],
            ['callbacks',[
                'value' => [
                    'uncallable' => [$this,'notCallable']
                ],
                'setter_exception' => [
                    \InvalidArgumentException::class,
                    'Provided callback is not callable'
                ]
            ]],
        ];
    }

    public function testGetBodyText()
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $target =  new StringTemplateMessage();
        $target->setTranslator($translator);

        $translator->expects($this->any())
            ->method('translate')
            ->with('Some Subject')
            ->willReturn('Translated Subject')
        ;
        $body = <<<EOC
++Subject: Some Subject++
Hello World ##name##!
Welcome to ##app##!
##callback##
##object## Callback
EOC;
        $target->setBody($body);

        // test ::getBodyText() without variables
        $this->assertEquals(
            "Hello World ##name##!"
            ."\nWelcome to ##app##!"
            ."\n##callback##"
            ."\n##object## Callback",
            $target->getBodyText()
        );

        $target->setVariable('name', 'Foo Bar');
        $target->setVariable('app', 'Yawik');
        $this->assertEquals(
            "Hello World Foo Bar!".
            "\nWelcome to Yawik!".
            "\n##callback##".
            "\n##object## Callback",
            $target->getBodyText()
        );
        $this->assertEquals(
            'Translated Subject',
            trim($target->getSubject())
        );

        $target->setCallback('callback', function () {
            return 'Hello From Callback!';
        });
        $target->setCallback('object', 'getSubject');
        $content = $target->getBodyText();

        $this->assertContains('Hello From Callback!', $content);
        $this->assertContains(
            'Translated Subject Callback',
            $content
        );
    }
}

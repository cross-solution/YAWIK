<?php
namespace CoreTest\Form\View\Helper;

use Core\Form\View\Helper\FormDatePicker;
use PHPUnit\Framework\TestCase;
use Core\Form\View\Helper\FormDatePicker as DatePickerHelper;
use Core\Form\Element\DatePicker as DatePickerElement;
use Laminas\View\Renderer\PhpRenderer as View;
use Laminas\I18n\Translator\TranslatorInterface as Translator;

/**
 * @coversDefaultClass Core\Form\View\Helper\FormDatePicker
 */
class FormDatePickerTest extends TestCase
{

    /**
     * @var DatePickerHelper
     */
    protected $datePickerHelper;
    
    /**
     * @see PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        $view = new View();

        $paramsPluginMock = $this->getMockBuilder('\Core\View\Helper\Params')->disableOriginalConstructor()->getMock();
        $pluginManager = $view->getHelperPluginManager();
        $pluginManager->setService('params', $paramsPluginMock);
        $view->setHelperPluginManager($pluginManager);

        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        $translator->method('translate')
            ->willReturnArgument(0);
        
        $this->datePickerHelper = new DatePickerHelper();
        $this->datePickerHelper->setView($view);
        $this->datePickerHelper->setTranslator($translator);
    }

    /**
     * @covers ::render
     */
    public function testRenderDateFormatAttribute()
    {
        $element = new DatePickerElement('elementName');

        // test default date forma
        $expectedResultString = $this->datePickerHelper->createAttributesString(['data-date-format' => FormDatePicker::DEFAULT_DATE_FORMAT]);
        $result = $this->datePickerHelper->render($element);

        $this->assertIsString($result);
        $this->assertContains($expectedResultString, $result);

        // test different date format
        $element->setAttribute('data-date-format', 'dd/m/yyyy');
        $expectedResultString = $this->datePickerHelper->createAttributesString(['data-date-format' => 'dd/m/yyyy']);
        $result = $this->datePickerHelper->render($element);

        $this->assertIsString($result);
        $this->assertContains($expectedResultString, $result);
    }
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller\Plugin;

use Core\Form\TextSearchForm;
use Core\Form\TextSearchFormFieldset;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use Zend\Form\FormElementManager;

/**
 * Tests for \Core\Controller\Plugin\SearchForm
 * 
 * @covers \Core\Controller\Plugin\SearchForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 * @group Core.Controller.Plugin
 */
class SearchFormTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait;

    protected $formElementManagerMock;

    protected $target = [
        'class' => '\Core\Controller\Plugin\SearchForm',
        'mock' => [
            'testInvokationProxiesToGet' => [ 'get' ],
        ],
    ];

    protected $inheritance = [ '\Zend\Mvc\Controller\Plugin\AbstractPlugin' ];

    protected function getTargetArgs()
    {
        switch ($this->getName(false)) {
            case 'testInheritance':
                return [ 'formElementManager' => '@\Zend\Form\FormElementManager' ];
                break;

            case 'testInvokationProxiesToGet':
                return false;
                break;

            default:
                $this->formElementManagerMock = $this->getMockBuilder('\Zend\Form\FormElementManager')
                    ->setMethods(['get'])
                    ->getMock();
                return [$this->formElementManagerMock];
                break;
        }
    }

    public function testInvokationProxiesToGet()
    {
        $elements = 'TestElements';
        $buttons  = 'TestButtons';

        $this->target->expects($this->once())->method('get')->with($elements, $buttons);

        $this->target->__invoke($elements, $buttons);
    }

    private function setControllerMock($params = [])
    {
        $query = $this->getMockBuilder('\Zend\Stdlib\Parameters')->disableOriginalConstructor()->setMethods(['toArray'])->getMock();
        $query->expects($this->once())->method('toArray')->willReturn($params);

        $request = $this->getMockBuilder('\Zend\Http\Request')->disableOriginalConstructor()->setMethods(['getQuery'])->getMock();
        $request->expects($this->once())->method('getQuery')->willReturn($query);

        $controller = $this
            ->getMockForAbstractClass('\Zend\Mvc\Controller\AbstractActionController', [], '', false, true, true, ['getRequest']);

        $controller->expects($this->once())->method('getRequest')->willReturn($request);

        $this->target->setController($controller);
        return $controller;

    }

    public function testGetIsAbleToPassOptionsAlongToTheFormElementManager()
    {
        $params = ['page' => 1234, 'test' => 'works'];
        $this->setControllerMock($params);

        $elementsOpt = ['some_options' => 'some_value'];
        $elementsFs = [ 'Test/Elements', $elementsOpt];

        $form = $this->getMockBuilder(TextSearchForm::class)
            ->setMethods(['setSearchParams'])
            ->getMock();
        $form->expects($this->once())->method('setSearchParams')->with($params);

        $this->formElementManagerMock
            ->expects($this->once())
            ->method('get')
            ->with($elementsFs[0], $elementsOpt)
            ->willReturn($form);

        $actual = $this->target->get($elementsFs);

        $this->assertSame($form, $actual);

    }

    public function testGetPassesEmptyOptionsArrayIfElementsFieldsetIsAString()
    {
        $form = new TextSearchForm();
        $elements = 'Test/Elements';
        $this->formElementManagerMock
            ->expects($this->once())
            ->method('get')
            ->with($elements, [])
            ->willReturn($form);

        $this->setControllerMock();

        $this->target->get($elements);
    }

    public function testGetCreatesTextSearchFormIfOnlyAFieldsetIsPassed()
    {
        $form = new TextSearchFormFieldset();
        $elements = 'Test/Elements';
        $this->setControllerMock();
        $expectFormGet = [
            'elements_fieldset' => $form,
        ];

        $this->formElementManagerMock
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [$elements], ['Core/TextSearch', $expectFormGet]
            )
            ->will($this->onConsecutiveCalls($form, new TextSearchForm()));

        $this->target->get($elements);
    }

    public function testGetPassesButtonsFieldset()
    {
        $elementsFs = new TextSearchFormFieldset();
        $elements = 'Test/Elements';
        $buttons  = 'Test/Buttons';
        $this->setControllerMock();
        $expectFormGet = [
            'elements_fieldset' => $elementsFs,
            'buttons_fieldset' => $buttons,
        ];

        $this->formElementManagerMock
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
            [$elements], ['Core/TextSearch', $expectFormGet]
            )
            ->will($this->onConsecutiveCalls($elementsFs, new TextSearchForm()));

        $this->target->get($elements, $buttons);
    }
}

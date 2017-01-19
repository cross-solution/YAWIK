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
use CoreTestUtils\TestCase\TestInheritanceTrait;
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
    use TestInheritanceTrait;

    protected $formElementManagerMock;

    protected $target = [
        'class' => '\Core\Controller\Plugin\SearchForm',
        'args' => 'getTargetArgs',
        '@testInvokationProxiesToGet' => [
            'mock' => [ 'get' ],
            'args' => false,
        ],
        '@testInheritance' => [
            'args' => [ 'formElementManager' => '@\Zend\Form\FormElementManager' ],
        ],
    ];

    protected $inheritance = [ '\Zend\Mvc\Controller\Plugin\AbstractPlugin' ];

    protected function getTargetArgs()
    {
        $this->formElementManagerMock = $this
            ->getMockBuilder('\Zend\Form\FormElementManager')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock()
        ;
        return [$this->formElementManagerMock];
    }

    public function testInvokationProxiesToGet()
    {
        $form = 'TestForm';
        $options  = ['test' => 'test'];

        $this->target->expects($this->once())->method('get')->with($form, $options);

        $this->target->__invoke($form, $options);
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

    public function testGetFetchesFormFromFormElementManager()
    {
        $params = ['page' => 1234, 'test' => 'works'];
        $this->setControllerMock($params);

        $formOpt = ['some_options' => 'some_value'];
        $formName = 'Test/Elements';

        $form = $this->getMockBuilder(TextSearchForm::class)
            ->setMethods(['setSearchParams'])
            ->getMock();
        $form->expects($this->once())->method('setSearchParams')->with($params);

        $this->formElementManagerMock
            ->expects($this->once())
            ->method('get')
            ->with($formName, $formOpt)
            ->willReturn($form);

        $actual = $this->target->get($formName, $formOpt);

        $this->assertSame($form, $actual);

    }

    public function testGetJustSetSearchParamsOnFormObject()
    {
        $params = ['page' => 1234, 'test' => 'works'];
        $this->setControllerMock($params);


        $form = $this->getMockBuilder(TextSearchForm::class)
                     ->setMethods(['setSearchParams'])
                     ->getMock();
        $form->expects($this->once())->method('setSearchParams')->with($params);

        $this->formElementManagerMock
            ->expects($this->never())
            ->method('get');

        $actual = $this->target->get($form);

        $this->assertSame($form, $actual);

    }


}

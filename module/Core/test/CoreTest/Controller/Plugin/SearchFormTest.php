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

use PHPUnit\Framework\TestCase;

use Core\Controller\Plugin\SearchForm;
use CoreTestUtils\TestCase\AssertInheritanceTrait;
use Interop\Container\ContainerInterface;
use Zend\Form\Form;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\Hydrator\HydratorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Core\Controller\Plugin\SearchForm
 *
 * @covers \Core\Controller\Plugin\SearchForm
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 * @group Core.Controller.Plugin
 */
class SearchFormTest extends TestCase
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
            'args' => 'getTargetArgs',
        ],
    ];

    protected $inheritance = [ AbstractPlugin::class ];

    protected function getTargetArgs()
    {
        $this->formElementManagerMock = $this
            ->getMockBuilder(FormElementManager::class)
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
        $request = $this->getMockBuilder('\Zend\Http\Request')->disableOriginalConstructor()->setMethods(['getQuery'])->getMock();
        $request->expects($this->once())->method('getQuery')->willReturn(is_array($params) ? new Parameters($params) : $params);

        $controller = $this
            ->getMockForAbstractClass('\Zend\Mvc\Controller\AbstractActionController', [], '', false, true, true, ['getRequest']);

        $controller->expects($this->once())->method('getRequest')->willReturn($request);

        $this->target->setController($controller);
        return $controller;
    }

    public function testGetFetchesFormFromFormElementManager()
    {
        $params = new Parameters(['page' => 1234, 'test' => 'works']);
        $this->setControllerMock($params);

        $formOpt = ['some_options' => 'some_value'];
        $formName = 'Test/Elements';

        $formData = ['data' => 'value'];
        $hydrator = $this->getMockBuilder(HydratorInterface::class)
            ->setMethods(['hydrate', 'extract'])
            ->getMockForAbstractClass();
        $hydrator->expects($this->once())->method('extract')->with($this->isInstanceOf(Parameters::class))->willReturn($formData);
        $hydrator->expects($this->once())->method('hydrate')->with($formData, $params);

        $form = $this->getMockBuilder(Form::class)
            ->setMethods(['getHydrator', 'setData'])
            ->getMock();
        $form->expects($this->once())->method('gethydrator')->willReturn($hydrator);
        $form->expects($this->once())->method('setData')->with($formData);

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


        $formData = ['data' => 'value'];
        $hydrator = $this->getMockBuilder(HydratorInterface::class)
                         ->setMethods(['hydrate', 'extract'])
                         ->getMockForAbstractClass();
        $hydrator->expects($this->once())->method('extract')->with($this->isInstanceOf(Parameters::class))->willReturn($formData);
        $hydrator->expects($this->once())->method('hydrate')->with($formData, $this->isInstanceOf(Parameters::class));

        $form = $this->getMockBuilder(Form::class)
                     ->setMethods(['getHydrator', 'setData'])
                     ->getMock();
        $form->expects($this->once())->method('gethydrator')->willReturn($hydrator);
        $form->expects($this->once())->method('setData')->with($formData);
        $this->formElementManagerMock
            ->expects($this->never())
            ->method('get');

        $actual = $this->target->get($form);

        $this->assertSame($form, $actual);
    }
}

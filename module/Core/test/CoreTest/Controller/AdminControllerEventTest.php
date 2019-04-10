<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller;

use PHPUnit\Framework\TestCase;

use Core\Controller\AdminControllerEvent;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\View\Model\ViewModel;

/**
 * Tests for \Core\Controller\AdminControllerEvent
 *
 * @covers \Core\Controller\AdminControllerEvent
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 */
class AdminControllerEventTest extends TestCase
{
    use TestInheritanceTrait;

    protected $target = '\Core\Controller\AdminControllerEvent';

    protected $inheritance = [ '\Zend\EventManager\Event' ];

    public function testCreatesModelPriorityListUponCreation()
    {
        $target = new AdminControllerEvent();
        $this->assertAttributeInstanceOf('\Zend\Stdlib\PriorityList', 'models', $target);
    }

    public function testAddViewModelInsertsIntoList()
    {
        $model = new ViewModel();
        $this->assertSame($this->target, $this->target->addViewModel('test', $model), 'Fluent interface broken.');
        $this->target->addViewModel('test2', $model, 10);

        /* @var \Zend\Stdlib\PriorityList $list */
        $list = $this->target->getViewModels();
        $this->assertInstanceOf('\Zend\Stdlib\PriorityList', $list);

        $array = $list->toArray(\Zend\Stdlib\PriorityList::EXTR_BOTH);

        $this->assertArrayHasKey('test', $array);
        $test = $array['test'];
        $this->assertEquals(0, $test['priority']);
        $this->assertSame($model, $test['data']);

        $this->assertArrayHasKey('test2', $array);
        $test = $array['test2'];
        $this->assertEquals(10, $test['priority']);
        $this->assertSame($model, $test['data']);
    }

    public function provideAddViewTemplateTestData()
    {
        return [
            [ [ 'test', 'template' ], ['test', 'template', [], 0] ],
            [ [ 'test', 'template', [ 'var' => 'iable']], [ 'test', 'template', [ 'var' => 'iable'], 0] ],
            [ [ 'test', 'template', 10] , ['test', 'template', [], 10] ],
            [ [ 'test', 'template', [ 'var' => 'iable'], 10 ], [ 'test', 'template', [ 'var' => 'iable'], 10 ]],
        ];
    }

    /**
     * @dataProvider provideAddViewTemplateTestData
     *
     * @param $args
     * @param $expected
     */
    public function testAddViewTemplateCreatesViewModelAndPassesCorrectVariables($args, $expected)
    {
        $this->assertSame($this->target, call_user_func_array([$this->target, 'addViewTemplate'], $args), 'Fluent interface broken');

        $this->assertListEntry($expected[0], $expected[1], $expected[2], $expected[3]);
    }

    public function testAddViewVariablesThroesExceptionIfNoNameIsSpecified()
    {
        $this->expectException('\DomainException');
        $this->expectExceptionMessage('Key "name" must be');
        $this->target->addViewVariables(['no' => 'name']);
    }

    public function provideAddViewVariablesTestData()
    {
        return [
            [ [ 'test' ] , ['test', ['name' => 'test'], 0] ],
            [ [ 'test', [ 'var' => 'iable'] ], [ 'test', [ 'name' => 'test', 'var' => 'iable'], 0 ] ],
            [ [ 'test', [ 'var' => 'iable'], 10], [ 'test', [ 'name' => 'test', 'var' => 'iable'], 10 ]],
            [ [ [ 'name' => 'other', 'var' => 'test' ], 10], ['other', ['name' => 'other', 'var' => 'test' ], 10] ],
            [ [ 'test', ['name' => 'other'] ], [ 'test', [ 'name' => 'other' ], 0] ],
            [ [ 'test', 10 ], ['test', ['name' => 'test'], 10]],
        ];
    }

    /**
     * @dataProvider provideAddViewVariablesTestData
     *
     * @param $args
     * @param $expected
     */
    public function testAddViewVariablesCreatesModelAndPassesCorrectVariables($args, $expected)
    {
        $this->assertSame($this->target, call_user_func_array([$this->target, 'addViewVariables'], $args), 'Fluent interface broken');

        $this->assertListEntry($expected[0], 'core/admin/dashboard-widget', $expected[1], $expected[2]);
    }

    protected function assertListEntry($name, $template, $vars, $priority)
    {
        $list = $this->target->getViewModels();
        $array = $list->toArray(\Zend\Stdlib\PriorityList::EXTR_BOTH);

        $this->assertArrayHasKey($name, $array);

        $model = $array[$name]['data'];
        $this->assertInstanceOf('\Zend\View\Model\ViewModel', $model);
        $this->assertEquals($template, $model->getTemplate());
        $this->assertEquals($vars, $model->getVariables());
        $this->assertEquals($priority, $array[$name]['priority']);
    }
}

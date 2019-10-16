<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Controller\Plugin\PaginationBuilder;

use PHPUnit\Framework\TestCase;

use Core\Controller\Plugin\PaginationBuilder;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Core\Controller\Plugin\PaginationBuilder::getResult()
 *
 * @covers \Core\Controller\Plugin\PaginationBuilder
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Controller
 * @group Core.Controller.Plugin
 * @group Core.Controller.Plugin.PaginationBuilder
 */
class GetResultTest extends TestCase
{
    /**
     *
     *
     * @var \Zend\Http\Request
     */
    protected $request;

    protected $pluginMock;

    protected function setUp(): void
    {
        $this->target = new PaginationBuilder();

        $this->request = new Request();

        $pluginMock = new PluginMock();
        $this->pluginMock = $pluginMock;

        $controller = $this->getMockBuilder('\Zend\Mvc\Controller\AbstractController')
                           ->setMethods(['getRequest', 'plugin'])
                           ->getMockForAbstractClass();

        $controller->expects($this->any())->method('getRequest')->willReturn($this->request);
        $controller->expects($this->any())->method('plugin')->will($this->returnValueMap(
            [
                                                                           ['paginator', null, $pluginMock],
                                                                           ['paginationParams', null, $pluginMock],
                                                                           ['searchform', null, $pluginMock],
                                                                       ]
                                                                   ));

        $this->target->setController($controller);
    }

    public function testSettingAliasesViaArguments()
    {
        $paginatorAlias = 'paginatorAlias';
        $formAlias = 'formAlias';

        $this->target->form('form')->paginator('paginator');

        $result = $this->target->getResult($paginatorAlias, $formAlias);

        $this->assertArrayHasKey($paginatorAlias, $result);
        $this->assertArrayHasKey($formAlias, $result);
    }

    public function testPluginsAreCalledInRightOrder()
    {
        $this->target->paginator('pager')->params('namespace')->form('elements');

        $this->target->getResult();

        $expects = ['namespace', 'elements', 'pager'];

        foreach ($this->pluginMock->callstack as $args) {
            $expect = array_shift($expects);
            $this->assertEquals($expect, $args[0]);
        }
    }

    public function testFormPluginIsNotCalledIfAjaxRequest()
    {
        $headers = $this->request->getHeaders();
        $headers->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');


        $this->target->form('elements');
        $result = $this->target->getResult();

        $this->assertEmpty($result);
    }

    public function testQueryParameterAreFiltered()
    {
        $query = new Parameters([
            'a' => 'test',
            ',b' => 'test1,test2',
            '!c' => 'test1!test2',
            ',,d' => 'test1,test2',
        ]);
        $this->request->setQuery($query);

        $this->target->getResult();

        $actual = $query->toArray();

        $this->assertAttributeEquals(
            new Parameters([
                 'a' => 'test', 'b' => ['test1', 'test2'],
                 'c' => ['test1', 'test2'],
                 'd' => ['test1' => 1, 'test2' => 1]
             ]),
            'parameters',
            $this->target
        );
    }
}

class PluginMock
{
    public $callstack = [];
    public function __invoke()
    {
        $this->callstack[] = func_get_args();
    }
}

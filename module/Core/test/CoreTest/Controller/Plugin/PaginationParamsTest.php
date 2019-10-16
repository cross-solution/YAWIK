<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Applications\Repository\PaginationList;
use Core\Controller\AbstractCoreController;
use Core\Controller\Plugin\PaginationParams;
use Core\Repository\RepositoryInterface;
use Zend\Http\Request;
use Zend\Session\Container;
use Zend\Stdlib\Parameters;

/**
 * Class PaginationParamsTest
 * @package CoreTest\Controller\Plugin
 * @author Anthonius Munthi <me@itstoni.com>
 * @covers \Core\Controller\Plugin\PaginationParams
 * @since 0.30.1
 */
class PaginationParamsTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var PaginationParams
     */
    private $target;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->controller = $this->createMock(AbstractCoreController::class);
        $this->controller->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request)
        ;

        $target = new PaginationParams();
        $target->setController($this->controller);

        $this->target = $target;
    }

    public function testInvokation()
    {
        $mock = $this->getMockBuilder(PaginationParams::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParams','getList'])
            ->getMock()
        ;
        $repository = $this->createMock(RepositoryInterface::class);

        // setup getParams call
        $mock->expects($this->once())
            ->method('getParams')
            ->with('some/namespace', ['page'=>1], null)
            ->willReturn('getParams called')
        ;

        // setup getList call
        $mock->expects($this->exactly(2))
            ->method('getList')
            ->withConsecutive(
                ['some/namespace',[$this,'methodToTest']],
                ['some/namespace',$repository]
            )
            ->willReturn('getList called')
        ;

        /* @var \Core\Controller\Plugin\PaginationParams $mock */
        $this->assertSame(
            $mock,
            $mock(),
            '__invoke() returns itself when no param passed'
        );
        $this->assertEquals(
            'getParams called',
            $mock('some/namespace'),
            '__invoke() should call ::getParams with namespace'
        );
        $this->assertEquals(
            'getList called',
            $mock('some/namespace', [$this,'methodToTest']),
            '__invoke() should call ::getList when defaults is callable'
        );
        $this->assertEquals(
            'getList called',
            $mock('some/namespace', $repository),
            '__invoke() should call ::getList when $defaults is a Repository'
        );
    }

    public function methodToTest()
    {
        return 'methodToTest called';
    }

    public function testSetParams()
    {
        $mock = new PaginationParams();
        $params = ['key' => 'value'];
        $mock->setParams('namespace', ['key'=>'value']);
        $container = new Container('namespace');
        $this->assertEquals(
            $params,
            $container->params
        );
    }

    public function testGetParams()
    {
        $mock = $this->target;
        $request = $this->request;
        $params = new Parameters();

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($params)
        ;
        $params->set('clear', true);
        $output = $mock->getParams('namespace', []);
        $this->assertNull($output->get('clear'));
        // unset clear value to be used for next test
        $params->set('clear', false);


        $session = new Container('namespace');
        $session->params = ['session'=> 'session value'];

        $defaults = [
            'key' => 'value',
            'existing' => 'overwrite value',
            1 => 'session',
            2 => 'undefined'
        ];
        $params->set('existing', 'existing value');
        $output = $mock->getParams('namespace', $defaults, $params);
        $this->assertEquals(
            'value',
            $output->get('key'),
            '::getParams should set with default values'
        );
        $this->assertEquals(
            'existing value',
            $output->get('existing'),
            '::getParams should not set existing value with default'
        );
        $this->assertEquals(
            'session value',
            $output->get('session'),
            '::getParams should set from session'
        );
        $this->assertNull(
            $output->get('undefined'),
            '::getParams should set value to null if key not exists in session or $defaults'
        );
    }

    public function testGetList()
    {
        $session = new Container('namespace');
        $session->list = null;
        $mock = $this->target;

        $callback = [$this,'methodToTest'];
        $output = $mock->getList('namespace', $callback);
        $this->assertEquals(
            'methodToTest called',
            $output,
            '::getList should call defined callback'
        );

        $session->list = 'some value';
        $this->assertEquals(
            'some value',
            $mock->getList('namespace', $callback)
        );
    }

    public function testGetNeighbours()
    {
        $list = $this->createMock(PaginationList::class);
        $mock = $this->getMockBuilder(PaginationParams::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList'])
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('getList')
            ->with('namespace', 'callback')
            ->willReturn($list)
        ;

        $list->expects($this->once())
            ->method('setCurrent')
            ->with('id')
        ;
        $list->expects($this->once())
            ->method('getPrevious')
            ->willReturn('previous')
        ;
        $list->expects($this->once())
            ->method('getNext')
            ->willReturn('next')
        ;
        $this->assertEquals(
            ['previous','next'],
            $mock->getNeighbours('namespace', 'callback', 'id')
        );
    }
}

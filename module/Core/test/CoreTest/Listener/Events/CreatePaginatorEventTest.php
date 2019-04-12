<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\Listener\Events;

use PHPUnit\Framework\TestCase;

use Core\Listener\Events\CreatePaginatorEvent;
use Core\Paginator\PaginatorService;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Zend\Paginator\Paginator;

/**
 * Class CreatePaginatorEventTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @covers \Core\Listener\Events\CreatePaginatorEvent
 * @package CoreTest\Listener\Events
 */
class CreatePaginatorEventTest extends TestCase
{
    use TestSetterGetterTrait,SetupTargetTrait;

    protected $target = [
        'class' => CreatePaginatorEvent::class
    ];

    public function propertiesProvider()
    {
        $paginator = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $paginators = $this->getMockBuilder(PaginatorService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        return [
            ['paginatorParams',[
                'value'     =>['name'=>'value'],
                'default'   => array(),
            ]],
            ['paginatorName',[
                'value' => 'Some/Paginator',
                'default'   => null,
            ]],
            ['paginators',$paginators],
            ['paginator',$paginator]
        ];
    }

    public function testSetParams()
    {
        $paginators = $this->getMockBuilder(PaginatorService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $params = [
            'paginatorName' => 'Some/Name',
            'paginatorParams' => ['name' => 'value'],
            'paginators' => $paginators,
            'foo' => 'bar',
        ];

        /* @var CreatePaginatorEvent $target */
        $target = $this->target;
        $target->setParams($params);

        $this->assertEquals($params['paginatorName'], $target->getPaginatorName());
        $this->assertEquals($params['paginatorParams'], $target->getPaginatorParams());
        $this->assertEquals($params['paginators'], $target->getPaginators());
        $this->assertEquals(
            ['foo' => 'bar'],
            $target->getParams(),
            '::setParams() should unset params when it\'s already processed by setter'
        );
    }
}

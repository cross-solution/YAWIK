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

use Core\Controller\Plugin\ListQuery;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Doctrine\MongoDB\Query\Query;
use Interop\Container\ContainerInterface;

/**
 * Class ListQueryTest
 *
 * @package CoreTest\Controller\Plugin
 * @covers \Core\Controller\Plugin\ListQuery
 * @since 0.30.1
 */
class ListQueryTest extends TestCase
{
    use TestSetterGetterTrait;

    protected $target = [
        'class' => ListQuery::class
    ];

    protected $containerMock;

    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->target = new ListQuery($this->containerMock);
    }

    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $ob = ListQuery::factory($container);
        $this->assertInstanceOf(ListQuery::class, $ob);
    }

    public function propertiesProvider()
    {
        return [
            [
                'pageParamName',
                ['value' => 'somePage','setter_value'=>null,'default'=>'page']
            ],
            [
                'propertiesMap' ,
                ['value'=>'bar','setter_value'=>null,'default'=>array()]
            ],
            [
                'itemsPerPage',
                ['value' => 10,'setter_value'=>null,'default' => 25]
            ],
            [
                'queryKeysLowercased',
                ['value' => false,'setter_value'=>null,'default' => true]
            ],
            [
                'sortParamName',
                ['value' => 's','setter_value'=>null,'default' => 'sort']
            ],
        ];
    }
}

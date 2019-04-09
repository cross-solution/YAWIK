<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Repository\Filter;

use PHPUnit\Framework\TestCase;

use Applications\Repository\Filter\PaginationQuery;
use Applications\Repository\Filter\PaginationQueryFactory;
use Auth\AuthenticationService;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Applications\Repository\Filter\PaginationQueryFactory
 *
 * @covers  \Applications\Repository\Filter\PaginationQueryFactory
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author  Anthonius Munthi <me@itstoni.com>
 */
class PaginationQueryFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var PaginationQueryFactory|string
     */
    private $target = PaginationQueryFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $services = $this->getServiceManagerMock(['AuthenticationService' => ['service' => $auth, 'count_get' => 1]]);
        $plugins  = $this->getPluginManagerMock($services, 1);

        $filter   = $this->target->createService($services);

        $this->assertInstanceOf(PaginationQuery::class, $filter);
    }
}

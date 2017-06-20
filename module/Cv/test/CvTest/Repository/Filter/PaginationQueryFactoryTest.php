<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CvTest\Repository\Filter;

use Auth\AuthenticationService;
use Auth\Entity\User;
use Core\Paginator\PaginatorService;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Cv\Repository\Filter\PaginationQueryFactory;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Cv\Repository\Filter\PaginationQueryFactory
 * 
 * @covers \Cv\Repository\Filter\PaginationQueryFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Cv
 * @group Cv.Repository
 * @group Cv.Repository.Filter
 */
class PaginationQueryFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = PaginationQueryFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function provideUser()
    {
        return [
            [ null ],
            [ new User() ],
        ];
    }

    /**
     * @dataProvider provideUser
     *
     * @param $user
     */
    public function testInvokation($user)
    {
        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasIdentity', 'getUser'])
            ->getMock();

        $auth
            ->expects($this->once())
            ->method('hasIdentity')
            ->willReturn((bool) $user);

        if ($user) {
            $auth
                ->expects($this->once())
                ->method('getUser')
                ->willReturn($user);
        } else {
            $auth->expects($this->never())->method('getUser');
        }

        $services = $this->createServiceManagerMock(
            ['AuthenticationService' => $auth ]
        );

        $paginators = $this->createPluginManagerMock([], $services);

        $filter = $this->target->__invoke($services,'irrelevant');

        $this->assertAttributeSame($user, 'user', $filter);
    }
    
}
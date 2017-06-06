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

use Applications\Repository\Filter\PaginationQuery;
use Auth\AuthenticationService;
use Auth\Entity\User;
use Core\Repository\Filter\AbstractPaginationQuery;
use CoreTestUtils\TestCase\TestDefaultAttributesTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\MongoDB\Query\Builder;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Applications\Repository\Filter\PaginationQuery
 * 
 * @covers \Applications\Repository\Filter\PaginationQuery
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class PaginationQueryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestDefaultAttributesTrait;

    /**
     *
     *
     * @var PaginationQuery|array
     */
    private $target = [
        PaginationQuery::class,
        'getTargetArgs',
        '@testCreateQuery' => [
            'args' => 'getTargetArgsForCreateQueryTest',
        ],
    ];

    private $inheritance = [ AbstractPaginationQuery::class ];

    private $attributes = [
        'repositoryName'    => 'Applications/Application',
        'sortPropertiesMap' => [ 'date' => 'dateCreated.date' ]
    ];

    private $authMock;

    private function getTargetArgs()
    {
        $this->authMock = $this->getMockBuilder(AuthenticationService::class)->disableOriginalConstructor()->getMock();
        return [ $this->authMock ];
    }

    private function getTargetArgsForCreateQueryTest()
    {
        $user = new User();
        $user->setId('PqtUser');

        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser' ])
            ->getMock()
        ;

        $auth->expects($this->any())->method('getUser')->willReturn($user);

        $this->authMock = $auth;

        return [$auth];
    }

    public function testConstruct()
    {
        $this->assertAttributeSame($this->authMock, 'auth', $this->target);
    }


    /**
     */
    public function testCreateQuery()
    {
        $qb1 = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['field', 'equals', 'notEqual', 'all', 'sort'])
            ->getMock();

        $params1 = new Parameters();
        $params2 = [
            'job' => '',
            'unread' => '',
            'q' => '',
            'status' => 'all',

        ];
        $params3 = [
            'job' => 'testJob',
            'unread' => true,
            'q' => 'test',
            'status' => 'some',
            'sort' => 'testSort',
        ];
        $qb2 = $this->getMockBuilder(Builder::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['field', 'equals', 'notEqual', 'all', 'sort'])
                    ->getMock();


        $qb1->expects($this->exactly(4))->method('field')->withConsecutive(['permissions.view'], ['isDraft'])->will($this->returnSelf());
        $qb1->expects($this->exactly(4))->method('equals')->withConsecutive(['PqtUser'], [false])->will($this->returnSelf());
        $qb1->expects($this->exactly(2))->method('sort')->with(['dateCreated.date' => -1])->will($this->returnSelf());

        $qb2->expects($this->exactly(6))->method('field')->withConsecutive(
            [ 'job'], [ 'readBy'], ['keywords'], ['permissions.view'], ['isDraft'], ['status.name']
        )->will($this->returnSelf());
        $qb2->expects($this->exactly(4))->method('equals')->withConsecutive(
            [$params3['job']], ['PqtUser'], [false], [$params3['status']]
        )->will($this->returnSelf());
        $qb2->expects($this->once())->method('notEqual')->with('PqtUser');
        $qb2->expects($this->once())->method('all')->with($this->callback(function($arg) { return is_array($arg) && $arg[0] instanceOf \MongoRegex; }));
        $qb2->expects($this->once())->method('sort')->with(['testSort' => 1]);
        $qbResult1 = $this->target->createQuery($params1, $qb1);
        $qbResult2 = $this->target->createQuery($params2, $qb1);
        $qbResult3 = $this->target->createQuery($params3, $qb2);

        $this->assertSame($qbResult1, $qb1);
        $this->assertSame($qbResult2, $qb1);
        $this->assertSame($qbResult3, $qb2);
    }
}

class PqtQbMock {


    public function __call($method, $args) {
        $this->callstack[] = [ $method, $args ];

        return $this;
    }
}
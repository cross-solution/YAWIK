<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Repository\Filter;

use PHPUnit\Framework\TestCase;


use Auth\Entity\User;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Core\Repository\Filter\AbstractPaginationQuery;
use Cv\Entity\Location;
use Cv\Entity\Status;
use Cv\Repository\Filter\PaginationQuery;
use Geo\Entity\Geometry\Point;

/**
 * Class PaginationQueryTest
 *
 * @covers \Cv\Repository\Filter\PaginationQuery
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Cv
 * @group Cv.Repository
 * @group Cv.Repository.Filter
 */
class PaginationQueryTest extends TestCase
{
    use TestInheritanceTrait;

    private $user;

    private $target = [
        PaginationQuery::class,
        'getTargetArgs',
    ];

    private $inheritance = [ AbstractPaginationQuery::class ];

    private function getTargetArgs()
    {
        switch ($this->getName(false)) {
            case 'testInheritance':
            case 'testDefaultPropertyValues':
                return [];
                break;

            case 'testConstructSetsUserProperty':
                $user = new User();
                break;

            default:
                $user = new User();
                $user->setId('testId');
                break;
        }

        $this->user = $user;
        return [ $user ];
    }

    public function testDefaultPropertyValues()
    {
        $this->assertAttributeSame('Cv/Cv', 'repositoryName', $this->target);
    }

    public function testConstructSetsUserProperty()
    {
        $this->assertAttributeSame($this->user, 'user', $this->target);
    }

    public function getQueryBuilderMock($mode = null, $params = null)
    {
        $qb = $this
            ->getMockBuilder('\Doctrine\ODM\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this
            ->getMockBuilder('\Doctrine\ODM\MongoDB\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock();


        $qb->expects($this->any())->method('expr')->willReturn($expr);

        $qbExpects = [];
        $exprExpects = [];

        if ('search' == $mode || true === $mode) {
            $exprExpects['operator'][] = [ 'with' => ['$text', ['$search' => strtolower($params['search'])]]];
            $exprExpects['getQuery'][] = [ 'return' => 'exprQuery' ];
            $qbExpects['field'][] = [ 'with' => [null]];
            $qbExpects['equals'][] = [ 'with' => ['exprQuery']];
        }

        if ('location' == $mode || true === $mode) {
            $coords = $params['l']->getCoordinates()->getCoordinates();
            $qbExpects['field'][] = ['with' => ['preferredJob.desiredLocations.coordinates']];
            $qbExpects['geoWithinCenter'][] = ['with' => [$coords[0], $coords[1],(float)$params['d'] / 100]];
        }

        $exprExpects['field'][] = [ 'with' => ['permissions.view']];
        $exprExpects['equals'][] = [ 'with' => [ $this->user->getId()]];
        $exprExpects['field'][] = [ 'with' => ['status.name']];
        $exprExpects['equals'][] = [ 'with' => [Status::PUBLIC_TO_ALL]];

        $qbExpects['addOr'][] = [ 'with' => [$expr] ];
        $qbExpects['addOr'][] = [ 'with' => [$expr] ];



        $configureMock = function ($mock, $expects) {
            foreach ($expects as $method => $spec) {
                $count = count($spec);
                $with = [];
                $return = [];
                foreach ($spec as $s) {
                    $with[] = isset($s['with']) ? $s['with'] : [];
                    $return[] = isset($s['return']) ? $s['return'] : $this->returnSelf();
                }

                $mockMethod = $mock
                    ->expects($this->exactly($count))
                    ->method($method);
                $mockMethod = call_user_func_array([$mockMethod, 'withConsecutive'], $with);
                $mockReturn = call_user_func_array([$this, 'onConsecutiveCalls'], $return);
                $mockMethod->will($mockReturn);
            }
        };

        $configureMock($qb, $qbExpects);
        $configureMock($expr, $exprExpects);
        return $qb;
    }

    public function provideCreateQueryTestData()
    {
        $loc = new Location();
        $loc->setCoordinates(new Point([1,1]));
        return [

            'woParams'    => [ null, null ],
            'emptySearch' => [ null, ['search' => '']],
            'search'      => [ 'search', ['search' => 'MusBeLowerCase']],
            'emptyLocation' => [ null, ['l' => new Location()]],
            'location'    => [ 'location', ['l' => $loc, 'd' => 10]],
            'all'         => [ true, ['search' => 'MustLowerThisOneToo', 'l' => $loc, 'd' => 5]],

        ];
    }

    /**
     * @dataProvider provideCreateQueryTestData
     */
    public function testCreateQuery($mode, $params)
    {
        $qb = $this->getQueryBuilderMock($mode, $params);
        $this->target->createQuery($params, $qb);
    }
}

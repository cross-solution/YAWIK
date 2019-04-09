<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Paginator;

use PHPUnit\Framework\TestCase;

use Applications\Paginator\JobSelectPaginator;
use Core\Paginator\Adapter\DoctrineMongoCursor;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\MongoDB\Aggregation\Builder;
use Doctrine\MongoDB\Query\Query;
use Jobs\Repository\Job;
use Zend\Paginator\Paginator;

/**
 * Tests for \Applications\Paginator\JobSelectPaginator
 *
 * @covers \Applications\Paginator\JobSelectPaginator
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class JobSelectPaginatorTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        JobSelectPaginator::class,
        'getTargetArgs',
        '@testInheritance' => ['as_reflection' => true],
    ];


    private $repository;

    private $inheritance = [ Paginator::class ];

    private function getTargetArgs()
    {
        $this->repository = $this->getMockBuilder(Job::class)->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])->getMock();

        return [$this->repository];
    }

    public function testConstruction()
    {
        $this->assertAttributeSame($this->repository, 'repository', $this->target);
    }

    public function testSearch()
    {
        $q = 'test';
        $qb = $this->getMockBuilder(Builder::class)->disableOriginalConstructor()->setMethods(['field', 'equals', 'getQuery'])->getMock();
        $qb->expects($this->once())->method('field')->with('title')->will($this->returnSelf());
        $qb->expects($this->once())->method('equals')->with(
            $this->callback(function ($value) use ($q) {
                return $value instanceof \MongoRegex && (String) $value == '/' . $q . '/i';
            })
        );
        $cursor = $this->getMockBuilder(\Doctrine\ODM\MongoDB\Cursor::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMockBuilder(Query::class)->disableOriginalConstructor()->getMock();
        $query->expects($this->once())->method('execute')->will($this->returnValue($cursor));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $this->repository->expects($this->once())->method('createQueryBuilder')->will($this->returnValue($qb));
        $this->target->search($q);

        $this->assertAttributeInstanceOf(DoctrineMongoCursor::class, 'adapter', $this->target);
    }
}

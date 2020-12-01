<?php

/**
 *

 */

declare(strict_types=1);

namespace JobsTest\Repository\Filter;

use Jobs\Repository\Filter\PaginationAdminQuery;
use MongoDB\BSON\ObjectId;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Promise\PromiseInterface;

/**
 * Tests for \Jobs\Repository\Filter\PaginationAdminQuery
 *
 * @covers \Jobs\Repository\Filter\PaginationAdminQuery
 * @group Jobs
 * @group Jobs.Repository
 * @group Jobs.Repository.Filter
 */
class PaginationAdminQueryTest extends TestCase
{
    private $target;
    private $queryBuilder;

    protected function setUp(): void
    {
        $this->target = new PaginationAdminQuery();
        $this->queryBuilder = $this->prophesize(\Doctrine\MongoDB\Query\Builder::class);
        $this->queryBuilder->sort('datePublishStart.date', -1)->shouldBeCalled();
    }

    public function testFieldIsDraftIsset()
    {
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();

        $this->target->createQuery([], $qb);
    }

    public function testStatusAllDoesNotSetSearchField()
    {
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->field('status.name')->shouldNotBeCalled();

        $this->target->createQuery(['status' => 'all'], $qb);
    }

    public function testStatusDoesSetStatusSearchField()
    {
        $status = 'active';
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->field('status.name')->shouldBeCalled()->wilLReturn($qb);
        $this->queryBuilder->equals($status)->shouldBeCalled();

        $this->target->createQuery(['status' => $status], $qb);
    }

    public function testCompanyIdDoesSetField()
    {
        $companyId = (new ObjectId())->__toString();
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->field('organization')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(Argument::that(function($arg) use ($companyId) { return (string) $arg === $companyId; }))->shouldBeCalled();

        $this->target->createQuery(['companyId' => $companyId], $qb);
    }

    public function testSortDoesCallSort()
    {
        $sort="one,-two";
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->sort(['one' => 1])->shouldBeCalled();
        $this->queryBuilder->sort(['two' => -1])->shouldBeCalled();

        $this->target->createQuery(['sort' => $sort], $qb);
    }

    public function testTextDoesFilterCorrectly()
    {
        $text="test text search";
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->text($text)->shouldBeCalled();

        $this->target->createQuery(['text' => $text], $qb);
    }

    public function testTextDoesFilterJobIdsCorrectly()
    {
        $text="job:jobId job:anotherid,yajid";
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->text($text)->shouldNotBeCalled();
        $this->queryBuilder->field('id')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->in(['jobId', 'anotherid', 'yajid'])->shouldBeCalled();

        $this->target->createQuery(['text' => $text], $qb);
    }

    public function testTextDoesFilterJobIdsAndTextCorrectly()
    {
        $text="job:jobId and some other text job:anotherid,yajid";
        $qb = $this->queryBuilder->reveal();
        $this->queryBuilder->field('isDraft')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->equals(false)->shouldBeCalled();
        $this->queryBuilder->text($text)->shouldNotBeCalled();
        $this->queryBuilder->field('id')->shouldBeCalled()->willReturn($qb);
        $this->queryBuilder->in(['jobId', 'anotherid', 'yajid'])->shouldBeCalled();
        $this->queryBuilder->text('and some other text')->shouldBeCalled();

        $this->target->createQuery(['text' => $text], $qb);
    }
}
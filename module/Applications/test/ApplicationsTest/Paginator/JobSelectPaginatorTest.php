<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace ApplicationsTest\Paginator;

use MongoDB\BSON\Regex;
use PHPUnit\Framework\TestCase;

use Applications\Paginator\JobSelectPaginator;
use Core\Paginator\Adapter\DoctrineMongoAdapter;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\ODM\MongoDB\Query\Builder;
use Jobs\Repository\Job as JobRepository;
use Laminas\Paginator\Paginator;

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

    /**
     * @var JobSelectPaginator
     */
    private $target = [
        JobSelectPaginator::class,
        'getTargetArgs',
        '@testInheritance' => ['as_reflection' => true],
    ];


    private $repository;

    private $inheritance = [ Paginator::class ];

    private function getTargetArgs()
    {
        $this->repository = $this->getMockBuilder(JobRepository::class)
            ->disableOriginalConstructor()
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
        $qb = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->setMethods(['field', 'equals', 'getQuery'])
            ->getMock()
        ;
        $qb->expects($this->once())->method('field')->with('title')->will($this->returnSelf());
        $qb->expects($this->once())->method('equals')->with(
            $this->callback(function ($value) use ($q) {
                if(!$value instanceof Regex){
                    throw new \Exception("Value is not instance of regex");
                }
                return $value instanceof Regex && (string) $value->getPattern() == '/' . $q . '/i';
            })
        );
        $this->repository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);
        $this->target->search($q);

        $this->assertAttributeInstanceOf(DoctrineMongoAdapter::class, 'adapter', $this->target);
    }
}

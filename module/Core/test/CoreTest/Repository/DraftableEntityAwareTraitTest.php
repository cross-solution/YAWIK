<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Repository;

use PHPUnit\Framework\TestCase;

use Core\Repository\DraftableEntityAwareInterface;
use Core\Repository\DraftableEntityAwareTrait;

/**
 * Tests for \Core\Repository\DraftableEntityAwareTrait
 *
 * @covers \Core\Repository\DraftableEntityAwareTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class DraftableEntityAwareTraitTest extends TestCase
{
    public function provideFindMethodsTestData()
    {
        return [
            [ 'findBy', [], 'findBy', ['isDraft' => false] ],
            [ 'findBy', [ 'isDraft' => null ], 'findBy', [] ],
            [ 'findBy', [ 'isDraft' => true ], 'findBy', [ 'isDraft' => true ] ],
            [ 'findBy', [ 'isDraft' => 'something'], 'findBy', ['isDraft' => 'something'] ],
            
            [ 'findDraftsBy', [], 'findBy', ['isDraft' => true]],
            [ 'findDraftsBy', ['isDraft' => null], 'findBy', ['isDraft' => true]],
            [ 'findDraftsBy', ['isDraft' => 'something'], 'findBy', ['isDraft' => true]],

            [ 'findOneBy', [], 'findOneBy', ['isDraft' => false] ],
            [ 'findOneBy', [ 'isDraft' => null ], 'findOneBy', [] ],
            [ 'findOneBy', [ 'isDraft' => true ], 'findOneBy', [ 'isDraft' => true ] ],
            [ 'findOneBy', [ 'isDraft' => 'something'], 'findOneBy', ['isDraft' => 'something'] ],

            [ 'findOneDraftBy', [], 'findOneBy', ['isDraft' => true]],
            [ 'findOneDraftBy', ['isDraft' => null], 'findOneBy', ['isDraft' => true]],
            [ 'findOneDraftBy', ['isDraft' => 'something'], 'findOneBy', ['isDraft' => true]],

            [ 'createDraft', [], 'create', ['isDraft' => true]],
            [ 'createDraft', ['isDraft' => false], 'create', ['isDraft' => true]],
        ];
    }

    /**
     * @dataProvider provideFindMethodsTestData
     *
     * @param $method
     * @param $criteria
     * @param $called
     * @param $expect
     */
    public function testFindMethodSetsCorrectCriteriaAndProxiesToParent($method, $criteria, $called, $expect)
    {
        $target = new DraftableEntityAwareRepository();

        $target->$method($criteria);

        $this->assertArrayHasKey($called, $target->calledMethods);
        $this->assertEquals($expect, $target->calledMethods[$called]);
    }

    public function testQueryBuilderCreation()
    {
        $qb = $this
            ->getMockBuilder('\Doctrine\MongoDB\Query\Builder')
            ->disableOriginalConstructor()
            ->setMethods(['field', 'equals'])
            ->getMock();

        $qb->expects($this->exactly(2))->method('field')->with('isDraft')->will($this->returnSelf());
        $qb->expects($this->exactly(2))->method('equals')->withConsecutive([true], [false]);

        RepositoryMock::$queryBuilderMock = $qb;

        $target = new DraftableEntityAwareRepository();

        $this->assertSame($qb, $target->createQueryBuilder(true));
        $this->assertSame($qb, $target->createQueryBuilder(false));
        $this->assertSame($qb, $target->createQueryBuilder(null));
    }
}

class RepositoryMock
{
    public static $queryBuilderMock;
    public $calledMethods = [];


    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $this->calledMethods['findBy'] = $criteria;
    }

    public function findDraftsBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        $this->calledMethods['findDraftsBy'] = $criteria;
    }

    public function findOneBy(array $criteria)
    {
        $this->calledMethods['findOneBy'] = $criteria;
    }

    public function findOneDraftBy(array $criteria)
    {
        $this->calledMethods['findOneDraftBy'] = $criteria;
    }

    public function createQueryBuilder($findDrafts = false)
    {
        return self::$queryBuilderMock;
    }

    public function create(array $data = null, $persist = false)
    {
        $this->calledMethods['create'] = $data;
    }
}

class DraftableEntityAwareRepository extends RepositoryMock implements DraftableEntityAwareInterface
{
    use DraftableEntityAwareTrait;
}

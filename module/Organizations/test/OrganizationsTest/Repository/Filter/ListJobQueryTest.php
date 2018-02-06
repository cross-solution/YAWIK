<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace OrganizationsTest\Repository\Filter;

use Doctrine\ODM\MongoDB\Query\Builder;
use Interop\Container\ContainerInterface;
use Organizations\Repository\Filter\ListJobQuery;

/**
 * Class ListJobQueryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package OrganizationsTest\Repository\Filter
 * @covers \Organizations\Repository\Filter\ListJobQuery
 * @since 0.30
 */
class ListJobQueryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateQuery()
    {
        $builder = $this->createMock(Builder::class);
        $target = new ListJobQuery();

        $builder->expects($this->exactly(3))
            ->method('field')
            ->withConsecutive(
                ['organization'],
                ['status.name'],
                ['isDraft']
            )
            ->will($this->returnSelf())
        ;

        $builder->expects($this->exactly(3))
            ->method('equals')
            ->withConsecutive(
                ['some-id'],
                ['active'],
                [false]
            )
            ->will($this->returnSelf())
        ;

        $params=['organization_id' => 'some-id'];
        $target->createQuery($params,$builder);
    }
}

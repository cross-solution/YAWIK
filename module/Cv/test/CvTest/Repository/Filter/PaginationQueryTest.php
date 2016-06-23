<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Repository\Filter;

use CoreTestUtils\TestCase\FunctionalTestCase;
use Cv\Repository\Filter\PaginationQuery;
use Doctrine\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\MongoDB\Query\Expr;
use Geo\Entity\Geometry\Point;
use Jobs\Entity\Location;

/**
 * Class PaginationQueryTest
 * @package CvTest\Repository\Filter
 * @covers  Cv\Repository\Filter\PaginationQuery
 * @covers  Cv\Repository\Filter\PaginationQueryFactory
 */
class PaginationQueryTest extends FunctionalTestCase
{
    /**
     * @var PaginationQuery
     */
    protected $target;

    public function setUp()
    {
        parent::setUp();
        if (!is_object($this->activeUser)) {
            $this->loginAsUser();
        }

        $this->target = new PaginationQuery($this->activeUser);
    }

    public function testCreateQueryWithDesiredWorkKeyword()
    {
        $dm = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dm->method('createQueryBuilder')
            ->with('Cv\Entity\Cv')
            ->willReturn($qb);
        $this->getApplicationServiceLocator()->setService('doctrine.documentmanager.odm_default', $dm);

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->once())
            ->method('expr')
            ->willReturn($expr);
        $expr
            ->expects($this->once())
            ->method('operator')
            ->with('$text', ['$search' => 'some text'])
            ->willReturn($expr);
        $qb->method('field')
            ->willReturn($qb);

        // start execute the mock!
        $pq = $this->target;
        $qb = $this->getDoctrine()->createQueryBuilder('Cv\Entity\Cv');

        $params = [];
        $params['search'] = 'some text';
        $pq->createQuery($params, $qb);
    }

    public function testCreateQueryWithLocationKeyword()
    {
        $dm = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $dm->method('createQueryBuilder')
            ->with('Cv\Entity\Cv')
            ->willReturn($qb);
        $this->getApplicationServiceLocator()->setService('doctrine.documentmanager.odm_default', $dm);

        $qb->expects($this->once())
            ->method('field')
            ->with('preferredJob.desiredLocations.coordinates')
            ->willReturn($qb);

        $qb->expects($this->once())
            ->method('geoWithinCenter')
            ->with(1, 2, (float)5 / 100);

        // start execute the mock!
        $pq = $this->target;
        $qb = $this->getDoctrine()->createQueryBuilder('Cv\Entity\Cv');


        $loc = new Location();
        $loc
            ->setCity('Winchester')
            ->setRegion('England')
            ->setPostalCode('S023 9AX')
            ->setCountry('United Kingdom')
            ->setCoordinates(new Point([1, 2]));
        $params = [];
        $params['location'] = $loc;
        $params['d'] = 5;
        $pq->createQuery($params, $qb);
    }
}

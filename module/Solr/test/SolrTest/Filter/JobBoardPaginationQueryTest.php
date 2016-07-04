<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Filter;

use Jobs\Entity\CoordinatesInterface;
use Jobs\Entity\Location;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Solr\Bridge\Manager;
use Solr\Filter\JobBoardPaginationQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobBoardPaginationQueryTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package SolrTest\Filter
 * @covers  Solr\Filter\JobBoardPaginationQuery
 * @covers  Solr\Filter\AbstractPaginationQuery
 */
class JobBoardPaginationQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobBoardPaginationQuery
     */
    protected $target;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    public function setUp()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sl = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['get','has','getServiceLocator'])
            ->getMock()
        ;
        $sl->method('getServiceLocator')->willReturn($sl);
        $sl->method('get')->with('Solr/Manager')->willReturn($manager);
        $this->target = JobBoardPaginationQuery::factory($sl);
        $this->manager = $manager;
    }

    public function testFactory()
    {
        $target = $this->target;
        $this->assertInstanceOf(
            JobBoardPaginationQuery::class,
            $target,
            '::factory should return a correct instance'
        );
    }

    public function testFilter()
    {
        $this->assertInstanceOf(
            \SolrQuery::class,
            $this->target->filter([]),
            '::filter should return a \SolrQuery object'
        );
    }

    public function testCreateQuery()
    {
        $query  = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['setQuery','addSortField','addFilterQuery'])
            ->getMock()
        ;
        $coordinates = $this->getMockBuilder(CoordinatesInterface::class)
            ->getMock()
        ;
        $location = $this->getMockBuilder(Location::class)
            ->getMock()
        ;
        $location->method('getCoordinates')->willReturn($coordinates);
        $coordinates->expects($this->once())
            ->method('getCoordinates')
            ->willReturn([1.2,2.1])
        ;

        // expect to setQuery
        $query
            ->expects($this->exactly(2))
            ->method('setQuery')
            ->withConsecutive(['*:*'],['title:some OR organizationName:some'])
        ;

        // expect to addSortField
        $query
            ->expects($this->exactly(2))
            ->method('addSortField')
            ->withConsecutive(
                ['title',Manager::SORT_ASCENDING],
                ['companyName',Manager::SORT_DESCENDING]
            )
        ;

        // expect to handle location
        $query
            ->expects($this->once())
            ->method('addFilterQuery')
            ->with('{!geofilt pt=1.2,2.1 sfield=latLon d=10}')
        ;

        $params1 = ['search' => '','sort'=>'title'];
        $params2 = ['search' => 'some','sort'=>'-company','location'=>$location,'d'=>10];
        $target = $this->target;
        $target->createQuery($params1,$query);
        $target->createQuery($params2,$query);
    }

    public function testConvertOrganizationName()
    {
        $target = $this->target;
        $job = $this->getMockBuilder($target->getEntityClass())
            ->getMock()
        ;
        $org = $this->getMockBuilder(Organization::class)->getMock();
        $job->expects($this->exactly(2))
            ->method('getOrganization')
            ->willReturnOnConsecutiveCalls(null,$org)
        ;
        $org->expects($this->once())
            ->method('setOrganizationName')
            ->with($this->isInstanceOf(OrganizationName::class))
        ;

        $target->convertOrganizationName($job,'some-name');
    }

    public function testConvertOrganizationLogo()
    {
        $target = $this->target;
        $job = $this->getMockBuilder($target->getEntityClass())
            ->getMock()
        ;
        $org = $this->getMockBuilder(Organization::class)->getMock();
        $job->expects($this->exactly(2))
            ->method('getOrganization')
            ->willReturnOnConsecutiveCalls(null,$org)
        ;
        $org->expects($this->once())
            ->method('setImage')
            ->with($this->isInstanceOf(OrganizationImage::class))
        ;

        $target->convertCompanyLogo($job,'/file/Organizations.OrganizationImage/5774cad3ecb2a162138b4568/logo.gif');
    }
}

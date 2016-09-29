<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace SolrTest\Filter\EntityToDocument;

use Solr\Filter\EntityToDocument\Job as JobFilter;
use Jobs\Entity\Job;
use Jobs\Entity\Location;
use Jobs\Entity\CoordinatesInterface;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;
use Organizations\Entity\OrganizationName;
use Solr\Bridge\Util;
use SolrInputDocument;
use DateTime;
use Core\Entity\Collection\ArrayCollection;
use stdClass;

/**
 * @coversDefaultClass \Solr\Filter\EntityToDocument\Job
 */
class JobTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JobFilter
     */
    protected $jobFilter;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->jobFilter = new JobFilter();
    }

    /**
     * @covers ::filter()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage must be instance of
     */
    public function testFilterWithInvalidJob()
    {
        $this->jobFilter->filter('invalid');
    }

    /**
     * @covers ::filter()
     */
    public function testFilterWithValidJob()
    {
        $date = new DateTime();
        $dateStr = Util::convertDateTime($date);

        $job = new Job();
        $job->setId('some-id')
            ->setTitle('some-title')
            ->setContactEmail('contact-email')
            ->setDateCreated($date)
            ->setDateModified($date)
            ->setDatePublishStart($date)
            ->setDatePublishEnd($date)
            ->setLink('http://test.link.org/job1')
            ->setLanguage('some-language')
            ->setApplyId('some-external-id');

        $document = $this->jobFilter->filter($job);
        
        $this->assertInstanceOf(SolrInputDocument::class, $document);
        $this->assertSame($job->getId(), $this->getDocumentValue($document, 'id'));
        $this->assertSame($job->getApplyId(), $this->getDocumentValue($document, 'applyId'));
        $this->assertSame('job', $this->getDocumentValue($document, 'entityName'));
        $this->assertSame($job->getTitle(), $this->getDocumentValue($document, 'title'));
        $this->assertSame($job->getContactEmail(), $this->getDocumentValue($document, 'applicationEmail'));
        $this->assertSame($job->getLink(), $this->getDocumentValue($document, 'link'));
        $this->assertSame($dateStr, $this->getDocumentValue($document, 'dateCreated'));
        $this->assertSame($dateStr, $this->getDocumentValue($document, 'dateModified'));
        $this->assertSame($dateStr, $this->getDocumentValue($document, 'datePublishStart'));
        $this->assertSame($dateStr, $this->getDocumentValue($document, 'datePublishEnd'));
        $this->assertFalse((bool)$this->getDocumentValue($document, 'isActive'));
        $this->assertSame($job->getLanguage(), $this->getDocumentValue($document, 'lang'));
    }
    
    /**
     * @covers ::filter()
     */
    public function testFilterWithJobWithOrganization()
    {
        $organization = $this->getMockBuilder(Organization::class)
            ->getMock();
        
        $job = new Job();
        $job->setOrganization($organization);
        
        $jobFilter = $this->getMockBuilder(JobFilter::class)
            ->setMethods(['processOrganization'])
            ->getMock();
        $jobFilter->expects($this->once())
            ->method('processOrganization')
            ->with($this->identicalTo($job), $this->isInstanceOf(SolrInputDocument::class));
        
        $jobFilter->filter($job);
    }
    
    /**
     * @covers ::getDocumentIds()
     * @covers ::getLocationDocumentId()
     */
    public function testGetDocumentIds()
    {
        $id = 'some-id';
        $job = new Job();
        $job->setId($id);
        
        $this->assertSame([$id], $this->jobFilter->getDocumentIds($job));
        
        $coordinate1 = 1.2;
        $coordinate2 = 2.1;
        $coordinatesWrapper = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getCoordinates'])
            ->getMock();
        $coordinatesWrapper->method('getCoordinates')
            ->willReturn([$coordinate1, $coordinate2]);
        $location = $this->getMockBuilder(Location::class)
            ->getMock();
        $location->method('getCoordinates')
            ->willReturn($coordinatesWrapper);
        $locations = new ArrayCollection([$location]);
        $job->setLocations($locations);
        
        $this->assertSame([$id, "{$id}-{$coordinate1},{$coordinate2}"], $this->jobFilter->getDocumentIds($job));
    }

    /**
     * @covers ::processOrganization()
     */
    public function testProcessOrganization()
    {
        $companyLogo = 'some-uri';
        $organizationName = 'some-name';
        $organizationId = 'some-id';
        
        $org = $this->getMockBuilder(Organization::class)
            ->getMock();
        $org->expects($this->once())
            ->method('getId')
            ->willReturn($organizationId);
        
        $orgName = $this->getMockBuilder(OrganizationName::class)
            ->getMock();
        $orgName->expects($this->once())
            ->method('getName')
            ->willReturn($organizationName);
        
        $orgImage = $this->getMockBuilder(OrganizationImage::class)
            ->getMock();
        $orgImage->expects($this->once())
            ->method('getUri')
            ->willReturn($companyLogo);

        $job = $this->getMockBuilder(Job::class)
            ->getMock();
        $job->method('getOrganization')
            ->willReturn($org);
        $org->method('getOrganizationName')
            ->willReturn($orgName);
        $org->method('getImage')
            ->willReturn($orgImage);

        $document = new SolrInputDocument();
        
        $this->jobFilter->processOrganization($job, $document);
        $this->assertSame($companyLogo, $this->getDocumentValue($document, 'companyLogo'));
        $this->assertSame($organizationName, $this->getDocumentValue($document, 'organizationName'));
        $this->assertSame($organizationId, $this->getDocumentValue($document, 'organizationId'));
    }

    /**
     * @covers ::processLocation()
     * @covers ::getLocationDocumentId()
     */
    public function testProcessLocation()
    {
        $locationText = 'some location';
        $coordinatesArray = [1.2,2.1];
        $coordinatesConverted = '1.2,2.1';
        $city = 'some city';
        $country = 'some country';
        $postalCode = 'some postal code';
        $region = 'some region';
        $job = $this->getMockBuilder(Job::class)
            ->setMethods(['getLocations'])
            ->getMock();
        $location = $this->getMockBuilder(Location::class)->getMock();
        $coordinates = $this->getMockBuilder(CoordinatesInterface::class)->getMock();
        $locations = [$location];
        
        $job->setId('job-id');
        $job->setLocation($locationText);
        $job->expects($this->once())
            ->method('getLocations')
            ->willReturn($locations);
        $location->method('getCoordinates')
            ->willReturn($coordinates);
        $location->expects($this->once())
            ->method('getCity')
            ->willReturn($city);
        $location->expects($this->once())
            ->method('getCountry')
            ->willReturn($country);
        $location->expects($this->once())
            ->method('getRegion')
            ->willReturn($region);
        $location->expects($this->once())
            ->method('getPostalCode')
            ->willReturn($postalCode);
        $coordinates->expects($this->once())
            ->method('getCoordinates')
            ->willReturn($coordinatesArray);
        
        $document = new SolrInputDocument();
            
        $this->jobFilter->processLocation($job, $document);
        $this->assertSame($job->getLocation(), $this->getDocumentValue($document, 'location'));
        $this->assertSame($region, $this->getDocumentValue($document, 'regionList'));
        $childDocuments = $document->getChildDocuments();
        $this->assertCount(count($locations), $childDocuments);
        $childDocument = reset($childDocuments);
        $this->assertSame('location', $this->getDocumentValue($childDocument, 'entityName'));
        $this->assertSame($coordinatesConverted, $this->getDocumentValue($childDocument, 'point'));
        $this->assertSame($coordinatesConverted, $this->getDocumentValue($childDocument, 'latLon'));
        $this->assertSame("{$job->getId()}-$coordinatesConverted", $this->getDocumentValue($childDocument, 'id'));
        $this->assertSame($city, $this->getDocumentValue($childDocument, 'city'));
        $this->assertSame($postalCode, $this->getDocumentValue($childDocument, 'postalCode'));
        $this->assertSame($country, $this->getDocumentValue($childDocument, 'country'));
        $this->assertSame($region, $this->getDocumentValue($childDocument, 'region'));
        
    }
    
    /**
     * @param SolrInputDocument $document
     * @param string $fieldName
     * @return mixed
     */
    protected function getDocumentValue(SolrInputDocument $document, $fieldName)
    {
        return reset($document->getField($fieldName)->values);
    }
}

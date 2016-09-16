<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace SolrTest\Entity;

use Solr\Entity\JobProxy;
use Jobs\Entity\JobInterface;
use ArrayObject;
use stdClass;
use Doctrine\Common\Collections\Collection;
use Jobs\Entity\AtsMode;
use Organizations\Entity\OrganizationInterface;
use Auth\Entity\UserInterface;
use Core\Entity\PermissionsInterface;

/**
 * @coversDefaultClass \Solr\Entity\JobProxy
 */
class JobProxyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var JobProxy
     */
    protected $jobProxy;
    
    /**
     * @var JobInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jobDecorated;
    
    /**
     * @var ArrayObject
     */
    protected $solrResult;

    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->jobDecorated = $this->getMockBuilder(JobInterface::class)
            ->getMock();
        $this->solrResult = new ArrayObject();
        $this->jobProxy = new JobProxy($this->jobDecorated, $this->solrResult);
    }

    /**
     * @covers ::__construct()
     * @covers ::getApplications()
     */
    public function testGetApplications()
    {
        $this->proxyGetCall('getApplications');
    }

    /**
     * @covers ::getApplyId()
     * @covers ::getSolrResultValue()
     */
    public function testGetApplyId()
    {
        $this->proxyGetCall('getApplyId', 'applyId');
    }

    /**
     * @covers ::getAtsEnabled()
     */
    public function testGetAtsEnabled()
    {
        $this->proxyGetCall('getAtsEnabled');
    }

    /**
     * @covers ::getAtsMode()
     */
    public function testGetAtsMode()
    {
        $this->proxyGetCall('getAtsMode');
    }

    /**
     * @covers ::getCompany()
     */
    public function testGetCompany()
    {
        $this->proxyGetCall('getCompany');
    }

    /**
     * @covers ::getContactEmail()
     */
    public function testGetContactEmail()
    {
        $this->proxyGetCall('getContactEmail', 'applicationEmail');
    }

    /**
     * @covers ::getDatePublishEnd()
     */
    public function testGetDatePublishEnd()
    {
        $date = '2016-03-03T15:47:34Z';
        $this->proxyGetCall('getDatePublishEnd');
        
        $this->solrResult['datePublishEnd'] = $date;
        $this->assertEquals(new \DateTime($date), $this->jobProxy->getDatePublishEnd());
    }

    /**
     * @covers ::getDatePublishStart()
     */
    public function testGetDatePublishStart()
    {
        $date = '2016-03-03T15:47:34Z';
        $this->proxyGetCall('getDatePublishStart');
        
        $this->solrResult['datePublishStart'] = $date;
        $this->assertEquals(new \DateTime($date), $this->jobProxy->getDatePublishStart());
    }

    /**
     * @covers ::getHistory()
     */
    public function testGetHistory()
    {
        $this->proxyGetCall('getHistory');
    }

    /**
     * @covers ::getLanguage()
     */
    public function testGetLanguage()
    {
        $this->proxyGetCall('getLanguage', 'lang');
    }

    /**
     * @covers ::getLink()
     */
    public function testGetLink()
    {
        $this->proxyGetCall('getLink', 'link');
    }

    /**
     * @covers ::getLocation()
     */
    public function testGetLocation()
    {
        $this->proxyGetCall('getLocation', 'location');
    }

    /**
     * @param mixed $locations
     * @param string|null $expected
     * @covers ::getLocation()
     * @dataProvider dataLocationOverridenBySolrLocations
     */
    public function testGetLocationOverridenBySolrLocations($locations, $expected)
    {
        $method = 'getLocation';
        $this->solrResult['locations'] = $locations;
        
        if (isset($expected)) {
            $this->jobDecorated->expects($this->never())
                ->method($method);
            
            $this->assertSame($expected, $this->jobProxy->$method());
        } else {
            $this->proxyGetCall($method);
        }
    }
    
    public function dataLocationOverridenBySolrLocations()
    {
        return [
            'invalid locations type' => ['', null],
            'non-existent "docs" key' => [new ArrayObject(), null],
            'empty "docs"' => [new ArrayObject(['docs' => []]), null],
            'valid "docs"' => [new ArrayObject(['docs' => [new ArrayObject(['city' => 'first '], ArrayObject::ARRAY_AS_PROPS), new ArrayObject(['city' => ' second'], ArrayObject::ARRAY_AS_PROPS)]]), 'first, second'],
        ];
    }

    /**
     * @covers ::getLocations()
     */
    public function testGetLocations()
    {
        $this->proxyGetCall('getLocations');
    }

    /**
     * @covers ::getOrganization()
     */
    public function testGetOrganization()
    {
        $this->proxyGetCall('getOrganization');
    }

    /**
     * @covers ::getPortals()
     */
    public function testGetPortals()
    {
        $this->proxyGetCall('getPortals');
    }

    /**
     * @covers ::getReference()
     */
    public function testGetReference()
    {
        $this->proxyGetCall('getReference');
    }

    /**
     * @covers ::getStatus()
     */
    public function testGetStatus()
    {
        $this->proxyGetCall('getStatus');
    }

    /**
     * @covers ::getTermsAccepted()
     */
    public function testGetTermsAccepted()
    {
        $this->proxyGetCall('getTermsAccepted');
    }

    /**
     * @covers ::getTitle()
     */
    public function testGetTitle()
    {
        $this->proxyGetCall('getTitle', 'title');
    }

    /**
     * @covers ::getUriApply()
     */
    public function testGetUriApply()
    {
        $this->proxyGetCall('getUriApply');
    }

    /**
     * @covers ::getUriPublisher()
     */
    public function testGetUriPublisher()
    {
        $this->proxyGetCall('getUriPublisher');
    }

    /**
     * @covers ::getUser()
     */
    public function testGetUser()
    {
        $this->proxyGetCall('getUser');
    }

    /**
     * @covers ::setApplications()
     */
    public function testSetApplications()
    {
        $applications = $this->getMockBuilder(Collection::class)
            ->getMock();
        
        $this->proxySetCall('setApplications', $applications);
    }

    /**
     * @covers ::setApplyId()
     */
    public function testSetApplyId()
    {
        $this->proxySetCall('setApplyId');
    }

    /**
     * @covers ::setAtsEnabled()
     */
    public function testSetAtsEnabled()
    {
        $this->proxySetCall('setAtsEnabled');
    }

    /**
     * @covers ::setAtsMode()
     */
    public function testSetAtsMode()
    {
        $atsMode = $this->getMockBuilder(AtsMode::class)
            ->getMock();
        
        $this->proxySetCall('setAtsMode', $atsMode);
    }

    /**
     * @covers ::setCompany()
     */
    public function testSetCompany()
    {
        $this->proxySetCall('setCompany');
    }

    /**
     * @covers ::setContactEmail()
     */
    public function testSetContactEmail()
    {
        $this->proxySetCall('setContactEmail');
    }

    /**
     * @covers ::setDatePublishEnd()
     */
    public function testSetDatePublishEnd()
    {
        $this->proxySetCall('setDatePublishEnd');
    }

    /**
     * @covers ::setDatePublishStart()
     */
    public function testSetDatePublishStart()
    {
        $this->proxySetCall('setDatePublishStart');
    }

    /**
     * @covers ::setHistory()
     */
    public function testSetHistory()
    {
        $history = $this->getMockBuilder(Collection::class)
            ->getMock();
        
        $this->proxySetCall('setHistory', $history);
    }

    /**
     * @covers ::setLanguage()
     */
    public function testSetLanguage()
    {
        $this->proxySetCall('setLanguage');
    }

    /**
     * @covers ::setLink()
     */
    public function testSetLink()
    {
        $this->proxySetCall('setLink');
    }

    /**
     * @covers ::setLocation()
     */
    public function testSetLocation()
    {
        $this->proxySetCall('setLocation');
    }

    /**
     * @covers ::setLocations()
     */
    public function testSetLocations()
    {
        $this->proxySetCall('setLocations');
    }

    /**
     * @covers ::setOrganization()
     */
    public function testSetOrganization()
    {
        $organization = $this->getMockBuilder(OrganizationInterface::class)
            ->getMock();
        
        $this->proxySetCall('setOrganization', $organization);
    }

    /**
     * @covers ::setPortals()
     */
    public function testSetPortals()
    {
        $this->proxySetCall('setPortals', ['first', 'second']);
    }

    /**
     * @covers ::setReference()
     */
    public function testSetReference()
    {
        $this->proxySetCall('setReference');
    }

    /**
     * @covers ::setStatus()
     */
    public function testSetStatus()
    {
        $this->proxySetCall('setStatus');
    }

    /**
     * @covers ::setTermsAccepted()
     */
    public function testSetTermsAccepted()
    {
        $this->proxySetCall('setTermsAccepted');
    }

    /**
     * @covers ::setTitle()
     */
    public function testSetTitle()
    {
        $this->proxySetCall('setTitle');
    }

    /**
     * @covers ::setUriApply()
     */
    public function testSetUriApply()
    {
        $this->proxySetCall('setUriApply');
    }

    /**
     * @covers ::setUriPublisher()
     */
    public function testSetUriPublisher()
    {
        $this->proxySetCall('setUriPublisher');
    }

    /**
     * @covers ::setUser()
     */
    public function testSetUser()
    {
        $user = $this->getMockBuilder(UserInterface::class)
            ->getMock();
        
        $this->proxySetCall('setUser', $user);
    }

    /**
     * @covers ::getResourceId()
     */
    public function testGetResourceId()
    {
        $this->proxyGetCall('getResourceId');
    }

    /**
     * @covers ::getPermissions()
     */
    public function testGetPermissions()
    {
        $this->proxyGetCall('getPermissions');
    }

    /**
     * @covers ::setPermissions()
     */
    public function testSetPermissions()
    {
        $permissions = $this->getMockBuilder(PermissionsInterface::class)
            ->getMock();
        
        $this->proxySetCall('setPermissions', $permissions);
    }
    
    /**
     * Tests a proxy getter call of a $method
     * Optionally tests precedence of fetching a value from a Solr result
     *
     * @param string $method
     * @param string $solrKey
     */
    protected function proxyGetCall($method, $solrKey = null)
    {
        $expected = new stdClass();
        
        $this->jobDecorated->expects($this->once())
            ->method($method)
            ->willReturn($expected);
        
        $this->assertSame($expected, $this->jobProxy->$method());
        
        if (isset($solrKey))
        {
            $expected = $solrKey . 'value';
            $this->solrResult[$solrKey] = $expected;
            
            $this->assertSame($expected, $this->jobProxy->$method());
        }
    }
    
    /**
     * Tests a proxy setter call of a $method
     *
     * @param string $method
     * @param mixed $parameter
     */
    protected function proxySetCall($method, $parameter = null)
    {
        $expected = new stdClass();
        $parameter = isset($parameter) ? $parameter : $method . 'Parameter';
        
        $this->jobDecorated->expects($this->once())
            ->method($method)
            ->with($this->identicalTo($parameter))
            ->willReturn($expected);
        
        $this->assertSame($expected, $this->jobProxy->$method($parameter));
    }
}

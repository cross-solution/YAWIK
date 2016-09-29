<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Controller;

use Core\Console\ProgressBar;
use Doctrine\MongoDB\CursorInterface;
use SolrClient;
use Jobs\Entity\Job;
use Jobs\Repository\Job as JobRepository;
use Solr\Controller\ConsoleController;
use stdClass;

/**
 * Class ConsoleControllerTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @package SolrTest\Controller
 * @coversDefaultClass \Solr\Controller\ConsoleController
 */
class ConsoleControllerTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var ConsoleController
     */
    protected $controller;
    
    /**
     * @var SolrClient|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;
    
    /**
     * @var CursorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cursor;
    
    /**
     * @var ProgressBar|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $progressBar;
    
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $progressBarFactory;
    
    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder(SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->cursor = $this->getMockBuilder(CursorInterface::class)
            ->getMock();
        
        $jobRepo = $this->getMockBuilder(JobRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $jobRepo->expects($this->any())
            ->method('findActiveJob')
            ->willReturn($this->cursor);
        
        $this->progressBar = $this->getMockBuilder(ProgressBar::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->progressBarFactory = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $this->progressBarFactory->expects($this->any())
            ->method('__invoke')
            ->willReturn($this->progressBar);

        $this->controller = new ConsoleController($this->client, $jobRepo, $this->progressBarFactory);
    }
    
    /**
     * @covers ::__construct()
     * @covers ::activeJobIndexAction()
     */
    public function testActiveJobIndexActionWithoutJobs()
    {
        $this->cursor->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $this->cursor->expects($this->never())
            ->method('rewind');
        
        $this->progressBarFactory->expects($this->never())
            ->method('__invoke');
            
        $this->progressBar->expects($this->never())
            ->method('update');
        
        $this->client->expects($this->never())
            ->method('addDocument');
        $this->client->expects($this->never())
            ->method('commit');
        $this->client->expects($this->never())
            ->method('optimize');
        
        $this->assertContains('no active job', $this->controller->activeJobIndexAction());
    }
    
    /**
     * @covers ::__construct()
     * @covers ::activeJobIndexAction()
     */
    public function testActiveJobIndexActionWithJobs()
    {
        $job = new Job();
        $count = 2;
        
        $this->cursor->expects($this->once())
            ->method('count')
            ->willReturn($count);
        $this->cursor->expects($this->exactly($count + 1))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);
        $this->cursor->expects($this->exactly($count))
            ->method('current')
            ->willReturn($job);
        
        $this->progressBar->expects($this->exactly($count))
            ->method('update')
            ->withConsecutive([1, 'Job 1 / 2'], [2, 'Job 2 / 2']);
        
        $this->client->expects($this->exactly($count))
            ->method('addDocument');
        $this->client->expects($this->once())
            ->method('commit');
        $this->client->expects($this->once())
            ->method('optimize');
        
        $this->assertEmpty(trim($this->controller->activeJobIndexAction()));
    }
    
    /**
     * @covers ::getProgressBarFactory()
     */
    public function testGetProgressBarFactory()
    {
        $progressBarFactory = $this->controller->getProgressBarFactory();
        $this->assertInternalType('callable', $progressBarFactory);
    }
}

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
use Core\Repository\RepositoryService;
use Doctrine\MongoDB\CursorInterface;
use Jobs\Repository\Job;
use Solr\Controller\ConsoleController;
use Solr\Listener\JobEventSubscriber;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;
/**
 * Class ConsoleControllerTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package SolrTest\Controller
 * @covers  Solr\Controller\ConsoleController
 */
class ConsoleControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testActiveJobIndexAction()
    {
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $jobSubscriber = $this->getMockBuilder(JobEventSubscriber::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $jobRepo = $this->getMockBuilder(Job::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $cursor = $this->getMockBuilder(CursorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $sl = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock()
        ;
        $sl->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['repositories'],['Solr/Listener/JobEventSubscriber'])
            ->willReturnOnConsecutiveCalls($repositories,$jobSubscriber)
        ;

        $job = new \Jobs\Entity\Job();
        $repositories->expects($this->once())
            ->method('get')
            ->with('Jobs/Job')
            ->willReturn($jobRepo)
        ;
        $jobRepo->expects($this->once())
            ->method('findActiveJob')
            ->willReturn($cursor)
        ;
        $cursor->expects($this->once())
            ->method('count')
            ->willReturn(2)
        ;
        $cursor->expects($this->exactly(3))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true,true,false)
        ;
        $cursor->expects($this->exactly(2))
            ->method('current')
            ->willReturn($job)
        ;
        $jobSubscriber->expects($this->exactly(2))
            ->method('consoleIndex')
        ;
        $progressBar = $this->getMockBuilder(ProgressBar::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $progressBar->expects($this->exactly(2))
            ->method('update')
            ->withConsecutive([1, 'Job 1 / 2'],[2, 'Job 2 / 2'])
        ;

        $target = $this->getMockBuilder(ConsoleController::class)
            ->setMethods(['createProgressBar'])
            ->getMock()
        ;
        $target->expects($this->once())
            ->method('createProgressBar')
            ->with(2)
            ->willReturn($progressBar)
        ;
        $target->setServiceLocator($sl);
        $target->activeJobIndexAction();
    }
}

<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace JobsTest\Listener;

use Zend\ServiceManager\ServiceManager;
use Core\Service\RestClient;
use Jobs\Listener\Publisher;
use Jobs\Listener\Events\JobEvent;
use Jobs\Entity\Job;

/**
 * Class PublisherTest
 * @package JobsTest\Listener
 */
class PublisherTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $target;

    /**
     * @var
     */
    protected $jobEvent;

    /**
     * @var
     */
    protected $job;

    /**
     * @var
     */
    protected $serviceManager;

    /**
     * @var
     */
    protected $restClient;

    /**
     *
     */
    public function setUp()
    {
        $this->target = new Publisher();

        /*
        $this->serviceManager = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serviceManager
            ->expects($this->exactly(1))

            ->method('has')
            ->with('Jobs/RestClient')
            ->willReturn(True);

        $this->serviceManager
            ->expects($this->exactly(1))
            ->method('get')
            ->with('Jobs/RestClient')
            ->willReturn($this->mockLog);




        //$this->serviceManager->expects($this->exactly(1))->method('get')->with('Jobs/RestClient')->will($this->restClient);

        //->getMock();

        $this->jobEvent = $this->getMockBuilder('\Jobs\Listener\Events\JobEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $this->job = $this->getMockBuilder('\Jobs\Entity\Job')
            ->disableOriginalConstructor()
            ->getMock();

        $this->restClient = $this->getMockBuilder('\Core\Service\RestClient')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->jobEvent->setJobEntity($this->job);
        */
    }


    /**
     * Test the different possibilities to send a job
     */
    public function testRestPost()
    {

        /*
        $this->target->restPost($this->jobEvent);
        $this->assertAttributeEquals(array(), 'publisher', $this->job);
        */
        $this->markTestIncomplete(
             'This test has not been implemented yet.'
        );

    }
} 
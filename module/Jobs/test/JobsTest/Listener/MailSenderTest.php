<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Listener;

use Auth\Entity\User;
use Core\Mail\HTMLTemplateMessage;
use Jobs\Entity\Job;
use Jobs\Listener\Events\JobEvent;
use Jobs\Listener\MailSender;

/**
 * Tests for MailSender listener
 *
 * @covers \Jobs\Listener\MailSender
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Listener
 */
class MailSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class under Test
     *
     * @var MailSender
     */
    private $target;

    private $mailServiceMock;

    private $targetOptions;

    private $jobEvent;

    public function setUp()
    {
        switch ($this->getName(false)) {
            case 'testImplementsListenerAggregateInterface':
                $this->target = $this->getMockBuilder('\Jobs\Listener\MailSender')->disableOriginalConstructor()->getMock();
                break;


            case 'testRequiresMailServiceAndOptionsInConstructorAndSetsInternalProperties':
            case 'testAttachsToAndDetachsFromJobEvents':
                $this->mailServiceMock = $this->getMockBuilder('\Core\Mail\MailService')->disableOriginalConstructor()->getMock();
                $this->targetOptions = array('siteName' => 'TestConstructor', 'adminEmail' => 'test@constructor');

                $this->target = new MailSender($this->mailServiceMock, $this->targetOptions);
                break;

            default:
                $this->mailServiceMock = $this->getMockBuilder('\Core\Mail\MailService')->disableOriginalConstructor()->getMock();
                $this->mailServiceMock->expects($this->atLeastOnce())
                                      ->method('send')
                                      ->with($this->callback(array($this, 'popMailMock')));
                $this->mailServiceMock->expects($this->any())
                                      ->method('get')->with('htmltemplate')
                                      ->will($this->returnCallback(array($this, 'pushMailMock')));


                $this->targetOptions = array('siteName' => 'Test', 'adminEmail' => 'test@admin');

                $this->target = new MailSender($this->mailServiceMock, $this->targetOptions);
                $user = new User();
                $user->getInfo()->setEmail('test@email');
                $user->getInfo()->setFirstName('TestFirstName');
                $user->getInfo()->setLastName('TestLastName');

                $job = new Job();
                $job->setUser($user);
                $job->setReference('testRef');

                $this->jobEvent = new JobEvent();
                $this->jobEvent->setJobEntity($job);

                $this->inspectMailsCount = 0;
        }
    }

    /**
     * @testdox Implements \Zend\EventManager\ListenerAggregateInterface
     */
    public function testImplementsListenerAggregateInterface()
    {
        $this->assertInstanceOf('\Zend\EventManager\ListenerAggregateInterface', $this->target);
    }

    public function testRequiresMailServiceAndOptionsInConstructorAndSetsInternalProperties()
    {
        $this->assertAttributeSame($this->mailServiceMock, 'mailer', $this->target);
        $this->assertAttributeEquals($this->targetOptions, 'options', $this->target);
    }

    public function testAttachsToAndDetachsFromJobEvents()
    {
        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                       ->disableOriginalConstructor()
                       ->getMock();

        $callback1 = [new MailSenderListenerMock(),'listen1'];
	    $callback2 = [new MailSenderListenerMock(),'listen2'];
	    $callback3 = [new MailSenderListenerMock(),'listen3'];
        $events->expects($this->exactly(3))
               ->method('attach')
               ->withConsecutive(
                    array(JobEvent::EVENT_JOB_CREATED, array($this->target, 'onJobCreated')),
                    array(JobEvent::EVENT_JOB_ACCEPTED, array($this->target, 'onJobAccepted')),
                    array(JobEvent::EVENT_JOB_REJECTED, array($this->target, 'onJobRejected'))
               )
               ->will($this->onConsecutiveCalls($callback1,$callback2,$callback3))
        ;

        $events->expects($this->exactly(3))
               ->method('detach')
               ->withConsecutive(
                    array($callback1), array($callback2), array($callback3)
               )->willReturn(true);

        $this->target->attach($events);
        $this->assertAttributeEquals(array($callback1, $callback2, $callback3), 'listeners', $this->target);

        $this->target->detach($events);
        $this->assertAttributeEquals(array(), 'listeners', $this->target);
    }

    public function testSendsMailsToRecruiterAndAdminOnJobCreatedEvent()
    {
        $job = $this->jobEvent->getJobEntity();
        $info = $job->getUser()->getInfo();
        $this->addExpectedMailValues('mail/job-created', 'A new job opening was created', 'test@admin', $job, 'Test');
        $this->addExpectedMailValues(
            'mail/job-pending', 'Your Job have been wrapped up for approval',
            array($info->getEmail(), $info->getDisplayName(false)), $job, 'Test'
        );

        $this->target->onJobCreated($this->jobEvent);
    }

    public function testSendsMailsToRecruiterOnJobAcceptedOrJobRejectedEvent()
    {
        $job = $this->jobEvent->getJobEntity();
        $this->jobEvent->setTarget($this->jobEvent);
        $info = $job->getUser()->getInfo();
        $to = array($info->getEmail(), $info->getDisplayName(false));

        $this->addExpectedMailValues('mail/job-accepted', 'Your job has been published', $to, $job, 'Test');
        $this->addExpectedMailValues('mail/job-rejected', 'Your job has been rejected', $to, $job, 'Test');

        $this->target->onJobAccepted($this->jobEvent);
        $this->target->onJobRejected($this->jobEvent);
    }

    public function pushMailMock()
    {
        $values = $this->expectedMailValues[$this->inspectMailsCount++];
        $mail = $this->getMockBuilder('\Core\Mail\HTMLTemplateMessage')->disableOriginalConstructor()->getMock();

        $mail->expects($this->once())->method('setTemplate')->with($values['template'])->will($this->returnSelf());
        $mail->expects($this->once())->method('setSubject')->with($values['subject'])->will($this->returnSelf());
        $mail->expects($this->once())->method('setVariables')->with($values['variables'])->will($this->returnSelf());
        if (is_array($values['to'])) {
            $mail->expects($this->once())->method('setTo')->with($values['to'][0], $values['to'][1])->will($this->returnSelf());
        } else {
            $mail->expects($this->once())->method('setTo')->with($values['to'])->will($this->returnSelf());
        }

        $this->mailMock = $mail;
        return $mail;
    }

    public function popMailMock()
    {
        return $this->mailMock;
    }

    private function addExpectedMailValues($template, $subject, $to, $job, $siteName)
    {
        $this->expectedMailValues[] = array(
            'template' => $template,
            'subject' => $subject,
            'to' => $to,
            'variables' => array(
                'job' => $job,
                'siteName' => $siteName
            )
        );
    }
}

class MailSenderListenerMock
{
	public function listen1(){}
	public function listen2(){}
	public function listen3(){}
}
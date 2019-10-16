<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CvTest\Repository;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Applications\Entity\Application;
use Applications\Entity\Contact as ApplicationContact;
use Applications\Entity\Attachment as ApplicationAttachment;
use Doctrine\Common\Collections\ArrayCollection;
use Cv\Entity\Attachment as CvAttachment;
use Cv\Entity\Cv;
use Cv\Repository\Cv as Repository;
use Jobs\Entity\Job;

/**
 * @author fedys
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
class CvCreateFromApplicationTest extends TestCase
{
    /**
     * @var Cv
     */
    protected $cv;
    
    /**
     * @var Repository
     */
    protected $repository;
    
    /**
     * @var User
     */
    protected $user;
    
    protected function setUp(): void
    {
        $this->cv = $this->getMockBuilder(Cv::class)
            ->disableOriginalConstructor()
            ->setMethods(['setContact'])
            ->getMock();
        
        $this->repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->repository->method('create')
            ->willReturn($this->cv);
        
        $this->user = new User();
    }
    
    public function testCreateCalled()
    {
        $this->repository->expects($this->once())
            ->method('create');

        $job = new Job();
        $user = new User();
        $user->setId('jobUser');
        $job->setUser($user);

        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['getContact'])
            ->getMock();
        $application->method('getContact')
            ->willReturn(new ApplicationContact());
        $application->setJob($job);
        $cv = $this->repository->createFromApplication($application, $this->user);
        $this->assertSame($this->cv, $cv);
    }
    
    public function testCopyContact()
    {
        // expect proper setting of user to application contact
        $applicationContactImage = $this->getMockBuilder(ApplicationAttachment::class)
            ->getMock();

        // expect calling of application contact getImage() method
        $applicationContact = $this->getMockBuilder(ApplicationContact::class)
            ->getMock();

        $job = new Job();
        $user = new User();
        $user->setId('jobUser');
        $job->setUser($user);

        // expect calling of application getContact() method
        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['getContact'])
            ->getMock();
        $application->expects($this->once())
            ->method('getContact')
            ->willReturn($applicationContact);
        $application->setJob($job);
        // expect proper setting of contact to CV
        $this->cv->expects($this->once())
            ->method('setContact')
            ->with($this->equalTo($applicationContact));
        
        $cv = $this->repository->createFromApplication($application, $this->user);
        $this->assertSame($this->cv, $cv);
    }
    
    public function testCopyAttachments()
    {
        $applicationAttachment1Data = [
            'getContent' => 'content',
            'getName' => 'name',
            'getType' => 'type',
            'getDateUploaded' => new \DateTime(),
        ];
        $applicationAttachment1 = $this->getMockBuilder(ApplicationAttachment::class)
            ->setMethods(array_keys($applicationAttachment1Data))
            ->getMock();
        
        foreach ($applicationAttachment1Data as $method => $return) {
            $applicationAttachment1->expects($this->once())
                ->method($method)
                ->willReturn($return);
        }
        
        $applicationAttachments = new ArrayCollection([$applicationAttachment1]);
        
        $this->repository->expects($this->once())
            ->method('create');

        $job = new Job();
        $user = new User();
        $user->setId('jobUser');
        $job->setUser($user);

        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['getContact', 'getAttachments'])
            ->getMock();
        $application->method('getContact')
            ->willReturn(new ApplicationContact());
        $application->expects($this->once())
            ->method('getAttachments')
            ->willReturn($applicationAttachments);
        $application->setJob($job);
        $cv = $this->repository->createFromApplication($application, $this->user);
        $this->assertSame($this->cv, $cv);
        $cvAttachments = $cv->getAttachments();
        $this->assertSame($applicationAttachments->count(), $cvAttachments->count());
        $cvAttachment1 = $cvAttachments->first();
        $this->assertInstanceOf(CvAttachment::class, $cvAttachment1);
        $this->assertSame($applicationAttachment1Data['getContent'], $cvAttachment1->getFile()->getBytes());
        $this->assertSame($applicationAttachment1Data['getName'], $cvAttachment1->getName());
        $this->assertSame($applicationAttachment1Data['getType'], $cvAttachment1->getType());
        $this->assertSame($applicationAttachment1Data['getDateUploaded'], $cvAttachment1->getDateUploaded());
        $this->assertSame($user, $cvAttachment1->getUser());
    }
}

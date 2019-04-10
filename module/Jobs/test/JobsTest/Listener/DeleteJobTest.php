<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Listener;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Core\Listener\Events\AjaxEvent;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Jobs\Listener\DeleteJob;
use Jobs\Repository\Job;
use Jobs\Entity\Job as JobEntity;
use Zend\Http\PhpEnvironment\Request;
use Zend\Permissions\Acl\Acl;
use Zend\Stdlib\Parameters;

/**
 * Tests for \Jobs\Listener\DeleteJob
 *
 * @covers \Jobs\Listener\DeleteJob
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Listener
 */
class DeleteJobTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var array|DeleteJob|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        DeleteJob::class,
        'getTargetArgs',
    ];

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repositoryMock;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $userMock;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aclMock;

    private function getTargetArgs()
    {
        $this->repositoryMock = $this->getMockBuilder(Job::class)->disableOriginalConstructor()->setMethods(['find'])->getMock();
        $this->userMock       = new User();
        $this->aclMock        = $this->getMockBuilder(Acl::class)->disableOriginalConstructor()->setMethods(['isAllowed'])->getMock();

        return [$this->repositoryMock, $this->userMock, $this->aclMock];
    }

    public function testConstruction()
    {
        $this->assertAttributeSame($this->repositoryMock, 'repository', $this->target);
        $this->assertAttributeSame($this->userMock, 'user', $this->target);
        $this->assertAttributeSame($this->aclMock, 'acl', $this->target);
    }

    private function getEvent($id = null)
    {
        $event = new AjaxEvent();
        $request = new Request();
        $query = new Parameters();

        if ($id) {
            $query->set('id', $id);
        }

        $request->setQuery($query);
        $event->setRequest($request);

        return $event;
    }


    public function testInvokationWithNoIdParameter()
    {
        $event = $this->getEvent();

        $this->repositoryMock->expects($this->never())->method('find');
        $this->assertEquals(['success' => false, 'status' => 'fail', 'error' => 'No id provided'], $this->target->__invoke($event));
    }

    public function testInvokationNoJobFound()
    {
        $id = 'testId';
        $this->repositoryMock->expects($this->once())->method('find')->with($id)->willReturn(null);
        $this->aclMock->expects($this->never())->method('isAllowed');
        $event = $this->getEvent($id);

        $this->assertEquals(['success' => false, 'status' => 'fail', 'error' => 'Job not found.'], $this->target->__invoke($event));
    }

    public function testInvokationNoPermissions()
    {
        /* @var \PHPUnit_Framework_MockObject_MockObject|JobEntity $job */
        $id = 'testId';
        $job = $this->getMockBuilder(JobEntity::class)->setMethods(['delete'])->getMock();
        $job->expects($this->never())->method('delete');
        $job->setId($id);
        $this->repositoryMock->expects($this->once())->method('find')->with($id)->willReturn($job);
        $this->aclMock->expects($this->once())->method('isAllowed')->with($this->userMock, $job, 'delete')->willReturn(false);
        $event = $this->getEvent($id);

        $this->assertEquals(['success' => false, 'status' => 'fail', 'error' => 'No permissions.'], $this->target->__invoke($event));
    }

    public function testInvokation()
    {
        /* @var \PHPUnit_Framework_MockObject_MockObject|JobEntity $job */
        $id = 'testId';
        $job = $this->getMockBuilder(JobEntity::class)->setMethods(['delete'])->getMock();
        $job->expects($this->once())->method('delete');
        $job->setId($id);
        $this->repositoryMock->expects($this->once())->method('find')->with($id)->willReturn($job);
        $this->aclMock->expects($this->once())->method('isAllowed')->with($this->userMock, $job, 'delete')->willReturn(true);
        $event = $this->getEvent($id);

        $this->assertEquals(['success' => true, 'status' => 'OK'], $this->target->__invoke($event));
    }
}

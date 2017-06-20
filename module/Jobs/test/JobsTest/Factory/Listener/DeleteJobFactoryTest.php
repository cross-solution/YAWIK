<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Listener;

use Auth\AuthenticationService;
use Auth\Entity\User;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Factory\Listener\DeleteJobFactory;
use Jobs\Listener\DeleteJob;
use Jobs\Repository\Job;
use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Jobs\Factory\Listener\DeleteJobFactory
 * 
 * @covers \Jobs\Factory\Listener\DeleteJobFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Listener
 */
class DeleteJobFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|DeleteJobFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        DeleteJobFactory::class,
        '@testCreateService' => ['mock' => ['__invoke']],
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokation()
    {
        $acl = new Acl();
        $user = new User();
        $auth = $this
	        ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMock()
        ;

        $auth->expects($this->once())
             ->method('getUser')
             ->will($this->returnValue($user))
        ;

        $repository = $this->getMockBuilder(Job::class)->disableOriginalConstructor()->getMock();

        $repositories = $this->createPluginManagerMock(['Jobs' => $repository]);

        $container = $this->createServiceManagerMock([
                'Acl' => $acl,
                'AuthenticationService' => $auth,
                'repositories' => $repositories
            ]);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(DeleteJob::class, $listener);
        $this->assertAttributeSame($repository, 'repository', $listener);
        $this->assertAttributeSame($user, 'user', $listener);
        $this->assertAttributeSame($acl, 'acl', $listener);
    }
}
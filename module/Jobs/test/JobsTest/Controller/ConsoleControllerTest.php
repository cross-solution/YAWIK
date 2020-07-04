<?php
/*
 * This file is part of the Omed Project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JobsTest\Controller;

use Core\Repository\RepositoryService;
use Jobs\Controller\ConsoleController;
use Jobs\Entity\Job;
use Jobs\Repository\Job as JobRepository;
use Laminas\Console\Adapter\AdapterInterface as ConsoleAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConsoleControllerTest extends TestCase
{
    /**
     * @var ConsoleController
     */
    private $target;

    /**
     * @var JobRepository
     */
    private $jobRepo;

    /**
     * @var MockObject|ConsoleAdapter
     */
    private $console;

    protected function setUp()
    {
        $this->jobRepo = $this->createMock(JobRepository::class);

        $repositories = $this->createMock(RepositoryService::class);
        $repositories->expects($this->once())
            ->method('get')
            ->with('Jobs/Job')
            ->willReturn($this->jobRepo);

        $this->console = $this->createMock(ConsoleAdapter::class);
        $this->target = $this->getMockBuilder(ConsoleController::class)
            ->setConstructorArgs([$repositories])
            ->setMethods(['params'])
            ->getMock()
        ;
        $this->target->setConsole($this->console);
    }

    /**
     * @see https://github.com/cross-solution/YAWIK/issues/442
     */
    public function testExpireJobsInfoAction()
    {
        $jobRepo = $this->jobRepo;
        $console = $this->console;
        $controller = $this->target;
        $controller->method('params')
            ->willReturnMap([
                ['days',30],
                ['limit',10],
                ['info',true]
            ]);

        $job = new Job();
        $job->setId('some-id');
        $job->setTitle('Some Title');
        $job->setCompany('Some Company');

        $jobRepo->expects($this->once())
            ->method('findBy')
            ->with($this->isType('array'),null,10,0)
            ->willReturn([$job]);

        $console->expects($this->exactly(2))
            ->method('writeLine')
            ->withConsecutive(
                ['1 Jobs',4,null],
                [$this->stringContains('some-id'),3,null]
            );

        $controller->expireJobsAction();

    }
}

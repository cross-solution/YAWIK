<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace OrganizationsTest\Repository\Filter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Auth\AuthenticationService;
use Auth\Entity\User;
use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\ODM\MongoDB\Query\Query;
use Jobs\Entity\StatusInterface;
use Jobs\Repository\Job as JobRepository;
use Organizations\Entity\Organization;
use Organizations\Repository\Filter\PaginationQuery;
use Laminas\Stdlib\Parameters;

/**
 * Class PaginationQueryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 * @package OrganizationsTest\Repository\Filter
 * @covers \Organizations\Repository\Filter\PaginationQuery
 */
class PaginationQueryTest extends TestCase
{
    /**
     * @var PaginationQuery
     */
    protected $target;

    /**
     * @var MockObject|JobRepository
     */
    protected $jobRepository;

    /**
     * @var MockObject|AuthenticationService
     */
    protected $authService;

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        $this->jobRepository = $this->createMock(JobRepository::class);
        $this->authService = $this->createMock(AuthenticationService::class);
        $this->target = new PaginationQuery($this->jobRepository, $this->authService);
    }

    public function testCreateQuery()
    {
        $builder = $this->createMock(QueryBuilder::class);
        $target = $this->target;
        $authService = $this->authService;
        $user = $this->createMock(User::class);

        $authService->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user->expects($this->once())
            ->method('getRole')
            ->willReturn(User::ROLE_RECRUITER)
        ;
        $builder->expects($this->any())
            ->method('field')
            ->willReturnMap([
                ['permissions.view',$builder]
            ])
        ;

        $builder->expects($this->once())
            ->method('text')
            ->with('some text')
            ->willReturn($builder)
        ;

        $builder->expects($this->once())
            ->method('sort')
            ->with(['dateCreated.date'=>-1])
        ;

        $params = [
            'q' => 'some text'
        ];
        $target->createQuery($params, $builder);
    }

    /*
    @FIXME: odm-module-3 deprecated test
    public function testCreateQueryForProfile()
    {
        $builder = $this->createMock(QueryBuilder::class);
        $target = $this->target;
        $jobRepository = $this->jobRepository;
        $query = $this->createMock(Query::class);

        $authService = $this->authService;
        $user = $this->createMock(User::class);

        $authService->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $user->expects($this->once())
            ->method('getRole')
            ->willReturn(User::ROLE_USER)
        ;

        $builder->expects($this->any())
            ->method('field')
            ->willReturnMap([
                ['profileSetting',$builder],
                ['id',$builder],
                ['organization',$builder],
                ['status.name',$builder],
                ['isDraft',$builder],
                ['profileSetting',$builder]
            ])
        ;

        $builder->expects($this->exactly(2))
            ->method('notIn')
            ->willReturnMap([
                [ [StatusInterface::EXPIRED, StatusInterface::INACTIVE], $builder],
                [ ['some-id'],$builder ]
            ])
        ;

        $builder->expects($this->any())
            ->method('equals')
            ->with($this->anything())
            ->willReturn($builder)
        ;

        $organization = new Organization();
        $organization->setId('some-id');
        $organization->setProfileSetting(Organization::PROFILE_ACTIVE_JOBS);

        $builder->expects($this->any())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $results = $this->createMock(Cursor::class);

        $query->expects($this->any())
            ->method('execute')
            ->willReturn($results)
        ;
        $results->expects($this->any())
            ->method('toArray')
            ->willReturn([$organization])
        ;

        $results->expects($this->once())
            ->method('count')
            ->willReturn(0)
        ;

        $jobRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($builder)
        ;
        $params = new Parameters([
            'type' => 'profile'
        ]);
        $target->createQuery($params, $builder);
    }
    */
}

<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Acl;

use PHPUnit\Framework\TestCase;


use Auth\Entity\UserInterface;
use Core\Acl\FileAccessAssertion;
use Core\Entity\FileInterface;
use Core\Entity\PermissionsInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Class FileAccessAssertionTest
 * @package CoreTest\Acl
 * @since 0.30.3
 * @author Anthonius Munthi <me@itstoni.com>
 */
class FileAccessAssertionTest extends TestCase
{
    /**
     * @var FileAccessAssertion
     */
    protected $target;

    protected function setUp(): void
    {
        parent::setUp();
        $this->target = new FileAccessAssertion();
    }

    /**
     * Test if assert returns false if:
     * $role is not instance of UserInterface
     * or $resource is not instance of FileInterface
     */
    public function testReturnFalseWhenRoleAndFileIsInvalid()
    {
        $roleInterface = $this->createMock(RoleInterface::class);
        $resourceInterface = $this->createMock(ResourceInterface::class);

        $userRole = $this->createMock(UserInterface::class);
        $file = $this->createMock(FileInterface::class);

        $acl = $this->createMock(Acl::class);

        $target = new FileAccessAssertion();
        $failMessage = 'Assert should return false when role is not a user or resource is not file';

        $this->assertFalse(
            $target->assert($acl, $roleInterface, $resourceInterface),
            $failMessage
        );

        $this->assertFalse(
            $target->assert($acl, $userRole, $resourceInterface),
            $failMessage
        );

        $this->assertFalse(
            $this->target->assert($acl, $roleInterface, $file),
            $failMessage
        );
    }

    public function testIsGranted()
    {
        $acl = $this->createMock(Acl::class);
        $role = $this->createMock(UserInterface::class);
        $file = $this->createMock(FileInterface::class);
        $permissions = $this->createMock(PermissionsInterface::class);

        $file
            ->expects($this->any())
            ->method('getPermissions')
            ->willReturn($permissions)
        ;

        $target = new FileAccessAssertion();

        $permissions->expects($this->once())
            ->method('isGranted')
            ->with($role, PermissionsInterface::PERMISSION_VIEW)
            ->willReturn(true)
        ;
        $this->assertTrue(
            $target->assert($acl, $role, $file, PermissionsInterface::PERMISSION_VIEW)
        );
    }
}
